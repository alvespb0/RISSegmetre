<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Study;
use App\Services\UploadLaudoSocService;

class SocJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(UploadLaudoSocService $service): void
    {
        $studies = Study::whereHas('laudo', function ($q) {
                            $q->where('ativo', true);
                        })
                        ->where('status', 'laudado')
                        ->whereNotNull('cod_sequencial_ficha')
                        ->where('enviado_soc', false)
                        ->get();

                        
        \Log::info('SOC Job iniciou processamento', [
            'total' => $studies->count(),
            'studies' => $studies->pluck('id'),
        ]);

        foreach($studies as $study){
            $retorno = $service->uploadFromStudy($study->id);

            if($retorno == true){
                $study->update([
                    'enviado_soc' => true
                ]);
            }
        }
    }
}
