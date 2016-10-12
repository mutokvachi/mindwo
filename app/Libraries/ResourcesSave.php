<?php

namespace App\Libraries
{

    use Illuminate\Support\Facades\File;
    use Config;
    use Log;

    /**
     *
     * Resursu datņu un katalogu saglabāšanas klase
     * Pievienojot datnes (un katalogus) no satura redaktora, tās saglabā CMS projekta folderī public/resources.
     * Ja nepieciešams, šī klase datnes kopē arī uz portāla projekta folderi (tātad, datņu dublēšana)
     */
    class ResourcesSave
    {

        /**
         * Kopē norādīto datni
         * 
         * @param  string $file_path Ceļš uz kopējamo datni
         * @return boolean Atgriež true, ja izdevās kopēt
         */
        public static function saveFile($file_path)
        {
            $paths = Config::get('dx.resources_copy_paths', array());
            $resource_folder = Config::get('dx.resources_folder_name', 'resources');

            foreach ($paths as $path) {

                $dest_path = $path . DIRECTORY_SEPARATOR . $resource_folder . ResourcesSave::getRelativePath($resource_folder, $file_path);

                if (!File::copy($file_path, $dest_path)) {
                    Log::info("Sistēmas kļūda! Nav iepsējams kopēt datni no '" . $file_path . "' uz '" . $dest_path . "'.");
                    return false;
                }
            }

            return true;
        }

        /**
         * Dzēš norādīto datni
         * 
         * @param  string $file_path Ceļš uz dzēšamo datni
         * @return boolean  Atgriež true, ja izdevās dzēst
         */
        public static function deleteFile($file_path)
        {
            $paths = Config::get('dx.resources_copy_paths', array());
            $resource_folder = Config::get('dx.resources_folder_name', 'resources');

            foreach ($paths as $path) {

                $dest_path = $path . DIRECTORY_SEPARATOR . $resource_folder . ResourcesSave::getRelativePath($resource_folder, $file_path);

                File::delete($dest_path);

                if (File::exists($dest_path)) {
                    Log::info("Sistēmas kļūda! Nav iepsējams dzēst datni '" . $dest_path . "'.");
                    return false;
                }
            }

            return true;
        }

        /**
         * Dzēš norādīto katalogu
         * 
         * @param  string $dir_path Ceļš uz dzēšamo katalogu
         * @return boolean Atgriež true, ja izdevās dzēst folderi
         */
        public static function deleteFolder($dir_path)
        {
            $paths = Config::get('dx.resources_copy_paths', array());
            $resource_folder = Config::get('dx.resources_folder_name', 'resources');

            foreach ($paths as $path) {

                $dest_path = $path . DIRECTORY_SEPARATOR . $resource_folder . ResourcesSave::getRelativePath($resource_folder, $dir_path);
                File::deleteDirectory($dest_path);

                if (File::isDirectory($dest_path)) {
                    Log::info("Sistēmas kļūda! Nav iepsējams dzēst katalogu '" . $dest_path . "'.");
                    return false;
                }
            }

            return true;
        }

        /**
         * Izveido katalogu
         * 
         * @param  string $dir_path Ceļš uz izveidojamo katalogu
         * @return boolean Atgriež true, ja izdevās izveidot folderi
         */
        public static function createFolder($dir_path)
        {
            $paths = Config::get('dx.resources_copy_paths', array());
            $resource_folder = Config::get('dx.resources_folder_name', 'resources');

            foreach ($paths as $path) {

                $dest_path = $path . DIRECTORY_SEPARATOR . $resource_folder . ResourcesSave::getRelativePath($resource_folder, $dir_path);

                if (!File::makeDirectory($dest_path)) {
                    Log::info("Sistēmas kļūda! Nav iepsējams izveidot katalogu '" . $dest_path . "'.");
                    return false;
                }
            }

            return true;
        }

        /**
         * Pārsauc norādīto katalogu vai datni
         * 
         * @param  string $old_path Ceļš uz pārsaucamo katalogu/datni
         * @param  string $new_path Jaunais kataloga/datnes ceļš
         * @return boolean Atgriež true, ja izdevās pārsaukt
         */
        public static function renameFileOrFolder($old_path, $new_path)
        {
            $paths = Config::get('dx.resources_copy_paths', array());
            $resource_folder = Config::get('dx.resources_folder_name', 'resources');

            foreach ($paths as $path) {

                $dest_path_old = $path . DIRECTORY_SEPARATOR . $resource_folder . ResourcesSave::getRelativePath($resource_folder, $old_path);
                $dest_path_new = $path . DIRECTORY_SEPARATOR . $resource_folder . ResourcesSave::getRelativePath($resource_folder, $new_path);

                if (!rename($dest_path_old, $dest_path_new)) {
                    Log::info("Sistēmas kļūda! Nav iepsējams pārsaukt katalogu/datni no '" . $dest_path_old . "' uz '" . $dest_path_new . "'.");
                    return false;
                }
            }

            return true;
        }

        /**
         * Pārbauda, vai norādītajā folderīr ir kāda datne
         * 
         * @param string $dir_path Pilnais ceļš uz folderi
         * @return boolean Atgriež False ja folderī un apakšfolderos nav neviena datne, un True, ja ir kaut viena datne
         */
        public static function isDirEmpy($dir_path)
        {
            return (count(File::allFiles($dir_path)) == 0);
        }

        /**
         * Izgūst datnes relatīvo ceļu, neiskaitot resursu root folderi
         * 
         * @param string $resource_folder Resursu foldera nosaukums
         * @param string $file_path Pilnais datnes ceļš
         * @return string Relatīvais ceļš. Sāka ar \
         */
        private static function getRelativePath($resource_folder, $file_path)
        {
            $pos = strpos($file_path, $resource_folder);

            return substr($file_path, $pos + strlen($resource_folder));
        }

    }

}