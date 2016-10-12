<?php

namespace App\Libraries\Structure
{
    use Illuminate\Support\Facades\File;
    use Webpatser\Uuid\Uuid;    
    use DB;
    use App\Exceptions;
    use Log;
    
    /**
     * Programmatūras projektējuma apraksta (PPA) PDF datnes ģenerēšana
     */
    class StructMethod_ppa_pdf extends StructMethod
    {
        
        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */
        public function initData()
        { 
        }

        /**
         * Atgriež reģistra dzēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */
        public function getFormHTML()
        {
            return view('structure.ppa.form', [
                        'form_guid' => $this->form_guid
                    ])->render();
        }

        /**
         * Ģenerē PPA
         * 
         * @return void
         */
        public function doMethod()
        {
            $folder_path = base_path() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR;
            
            $tmp_file = $folder_path . 'ppa_' . Uuid::generate(4) . '.pdf';
            $orig_file = $folder_path . 'ppa.pdf';
            
            $this->generatePPA($tmp_file);
            
            if (File::exists($orig_file)) {
                File::delete($orig_file);
            }
            
            if (!File::move($tmp_file, $orig_file))
            {
                throw new Exceptions\DXCustomException("Nav iespējams nokopēt datni " . $file . " uz katalogu " . $this->copy_dir . "!");
            }
        }
        
        /**
         * Ģenerē PPA PDF datni
         * 
         * @param string $tmp_file Pagaidu datnes nosaukums (pilnais ceļš)
         */
        private function generatePPA($tmp_file) {
            $pdf = \App::make('snappy.pdf.wrapper');
            
            // Uzstāda UTF8 šifrēšanu
            $pdf->setOption('encoding', 'utf-8');

            // Uzstāda malu izmērus
            $pdf->setOption('margin-left', '30mm');
            $pdf->setOption('margin-right', '20mm');
            $pdf->setOption('margin-top', '25mm');
            $pdf->setOption('margin-bottom', '20mm');

            $doc_generator = new DocGenerator();
            $doc_generator->is_html_return = true;
            
            $ppa = $doc_generator->generatePPA(false); 
        
            // Iegūst skatu ko eksportēs uz pdf
            $html = view('structure.ppa.document', [
                'html' => $ppa
            ])->render();

            // ielādē pdf objektā html
            $pdf->loadHTML($html);

            // Titullapa            
            $pdf->setOption('cover', view('structure.ppa.cover_page', [ 
                'title' => get_portal_config('PPA_DOC_TITLE'),
                'author' => get_portal_config('PPA_DOC_AUTHOR')
            ])->render());

            // Satura rādītājs
            $pdf->setOption('toc', true);
            $pdf->setOption('toc-header-text', 'Saturs');
            $pdf->setOption('toc-text-size-shrink', 0.95);
            //$pdf->setOption('toc-level-indentation', 3);
            
            // Lapu numerācija
            $pdf->setOption('footer-center', '[page]/[topage]');
            $pdf->setOption('footer-font-size', '8');
            $pdf->setOption('footer-font-name', 'Open Sans",sans-serif');                       
                    
            $pdf->save($tmp_file);
        }

    }

}