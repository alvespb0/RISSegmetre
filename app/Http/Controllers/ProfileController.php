<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateSignature(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'signature' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'max:2048',
            ],
        ]);

        $file = $request->file('signature');

        $baseDir = storage_path('app/signatures');

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        if ($user->signature_path) {
            $old = storage_path('app/' . $user->signature_path);
            if (file_exists($old)) {
                unlink($old);
            }
        }

        [$width, $height, $type] = getimagesize($file->getRealPath());

        switch ($type) {
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($file->getRealPath());
                break;
            default:
                abort(422, 'Formato de imagem inválido.');
        }

        $newWidth = 400;
        $newHeight = intval(($height / $width) * $newWidth);

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        imagecopyresampled(
            $dst,
            $src,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );

        $filename = uniqid('signature_') . '.png';
        $relativePath = 'signatures/' . $filename;

        // Salva no storage
        imagepng(
            $dst,
            storage_path('app/' . $relativePath),
            6
        );

        imagedestroy($src);
        imagedestroy($dst);

        $user->medico()->update([
            'signature_path' => $relativePath,
        ]);

        return back()->with('status', 'signature-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
