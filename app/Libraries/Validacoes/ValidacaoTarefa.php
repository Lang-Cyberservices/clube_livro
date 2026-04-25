<?php

namespace App\Libraries\Validacoes;


    class ValidacaoTarefa {
        public function tempoMinimoTarefa($data, ?string &$error = null): bool
        {
            $request = \Config\Services::request();
            
            if($request->getPost('inicioData') && $request->getPost('inicioHorario') && $request->getPost('fimData') && $request->getPost('fimHorario')){
                $inicio=$request->getPost('inicioData')." ".$request->getPost('inicioHorario');
                $fim=$request->getPost('fimData')." ".$request->getPost('fimHorario');
            

                $inicioObj= new \DateTimeImmutable($inicio);
                $fimObj= new \DateTimeImmutable($fim);

                if($inicioObj > $fimObj){
                    return false;
                }else{
                    return true;
                }
            
            }



        }

        public function horarioEncavalado($data, ?string &$error = null): bool
        {

            $inicio=$request->getPost('inicioData')." ".$request->getPost('inicioHorario');
            $fim=$request->getPost('fimData')." ".$request->getPost('fimHorario');

            $B = model('BancoHoras');

            $B ->where('inicio >=', $inicio)
               ->where('fim <=', $inicio);

            $ocorrencias = $B->countAllResults();

            if($ocorrencias >0){
                return false;
            }else{
                return true;
            }
        }
    }
