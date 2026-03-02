<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Serie;
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
        $series = Serie::whereHas('laudo', function ($q) {
                            $q->where('ativo', true);
                        })
                        ->where('status', 'laudado')
                        ->where('enviado_soc', false)
                        ->get();

                        
        \Log::info('SOC Job iniciou processamento', [
            'total' => $series->count(),
            'studies' => $series->pluck('id'),
        ]);

        foreach($series as $serie){
            $retorno = $service->uploadFromSerie($serie->id);

            if($retorno == true){
                $serie->update([
                    'enviado_soc' => true
                ]);
            }
        }
    }
}
