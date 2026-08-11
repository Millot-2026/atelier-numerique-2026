<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

class Skeletor {
    private $dicts = [];
    private $base_path;
    private $global_base_path;
    private $tree;

    public function __construct($export_name) {
        $slugName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $export_name)));
        // Export local (dans l'application Skeletor)
        $this->base_path = "export/" . $slugName . "/";
        // Export global (dans le dossier centralisé à la racine de la clé : _www/export/)
        // dirname(__DIR__) = skeletor-v1.0/ → dirname(dirname(__DIR__)) = _www/ ✅
        $this->global_base_path = dirname(dirname(__DIR__)) . "/export/" . $slugName . "/";

        if (!file_exists('dicts/structure.json') || !file_exists('dicts/classes.json')) {
            die("ERREUR : Fichiers dicts manquants.");
        }
        $this->dicts['struct'] = json_decode(file_get_contents('dicts/structure.json'), true);
        $this->dicts['classes'] = json_decode(file_get_contents('dicts/classes.json'), true);
    }

    public function arborate($tree) {
        $this->tree = $tree;
        
        // Création du dossier local s'il n'existe pas
        if (!file_exists($this->base_path)) { 
            mkdir($this->base_path, 0777, true); 
        }
        // Création du dossier global s'il n'existe pas
        if (!file_exists($this->global_base_path)) { 
            mkdir($this->global_base_path, 0777, true); 
        }

        // Injection du fichier _meta.json (source de vérité pour indexv2.php)
        $meta = json_encode(['source' => 'skeletor', 'generated_at' => date('c')], JSON_PRETTY_PRINT);
        file_put_contents($this->base_path . '_meta.json', $meta);
        file_put_contents($this->global_base_path . '_meta.json', $meta);

        foreach ($tree as $siteName => $structure) {
            $treeHtml = $this->generateTreeHtml($structure);
            // Processus local
            $this->processNode($this->base_path, $structure, 0, $treeHtml, $siteName, $this->slugify($siteName));
            // Processus global (racine de la clé)
            $this->processNode($this->global_base_path, $structure, 0, $treeHtml, $siteName, $this->slugify($siteName));
        }
    }

    private function generateTreeHtml($node) {
        $html = "<ul>";
        
        uksort($node, function($a, $b) use ($node) {
            $valA = is_array($node[$a]) ? '' : strtolower($node[$a]);
            $valB = is_array($node[$b]) ? '' : strtolower($node[$b]);
            if (str_starts_with($valA, 'index')) return -1;
            if (str_starts_with($valB, 'index')) return 1;
            return 0;
        });

        foreach ($node as $key => $value) {
            if (is_array($value)) {
                $dirName = is_numeric($key) ? 'dossier' : $key;
                $html .= "<li>📁 <strong>" . htmlspecialchars($dirName) . "</strong>";
                if (!empty($value)) {
                    $html .= $this->generateTreeHtml($value);
                }
                $html .= "</li>";
            } else {
                $html .= "<li>📄 " . htmlspecialchars($value) . "</li>";
            }
        }
        $html .= "</ul>";
        return $html;
    }

    private function processNode($currentPath, $node, $level, $treeHtml, $siteName, $siteSlug) {
        foreach ($node as $key => $value) {
            if (is_array($value)) {
                $dirName = is_numeric($key) ? 'dossier' : $key;
                $slugDir = $this->slugify($dirName);
                $subPath = $currentPath . $slugDir . "/";
                
                if (!file_exists($subPath)) {
                    mkdir($subPath, 0777, true);
                }
                
                $this->processNode($subPath, $value, $level + 1, $treeHtml, $siteName, $siteSlug);
            } else {
                $this->generateFile($currentPath, $value, $level, $treeHtml, $siteName, $siteSlug);
            }
        }
    }

    private function generateFile($path, $name, $level, $treeHtml, $siteName, $siteSlug) {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        $extensionsList = ['.php', '.html', '.css', '.js', '.json', '.sass', '.scss', '.md', '.sql'];
        $cleanName = $name;
        foreach ($extensionsList as $e) {
            if (str_ends_with(strtolower($cleanName), $e)) {
                $cleanName = substr($cleanName, 0, -strlen($e));
                break;
            }
        }
        
        $slugBase = $this->slugify($cleanName);
        $relPath = ($level == 0) ? "./" : str_repeat("../", $level);

        if ($extension === 'css') {
            $fileName = $slugBase . ".css";
            $defaultCss = "/* Style principal pour " . htmlspecialchars($siteName) . " */\n"
                    . "body {\n    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;\n"
                    . "    background-color: #f4f7f6;\n    color: #333;\n    margin: 0;\n    padding: 20px;\n}\n"
                    . "header { background: #2c3e50; color: white; padding: 20px; border-radius: 8px; text-align: center; }\n"
                    . "main { margin-top: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }\n\n"
                    . "/* Élément de test visuel (couleur moche pour valider le CSS) */\n"
                    . ".test-css-ok {\n    background-color: #ff00ff !important;\n    color: #ffff00 !important;\n    padding: 10px;\n    border: 5px dashed #000000;\n    font-weight: bold;\n    text-align: center;\n    margin-top: 15px;\n}";
            file_put_contents($path . $fileName, $defaultCss);
        } 
        elseif ($extension === 'js') {
            $fileName = $slugBase . ".js";
            file_put_contents($path . $fileName, "// Script JS initialisé pour " . htmlspecialchars($siteName));
        }
        elseif ($extension === 'html') {
            $fileName = $slugBase . ".html";
            $html = $this->prepareStandardHTML($siteName, $relPath, $cleanName);
            file_put_contents($path . $fileName, $html);
        }
        elseif ($extension === 'json') {
            $fileName = $slugBase . ".json";
            file_put_contents($path . $fileName, "{\n  \"name\": \"$cleanName\"\n}");
        }
        elseif ($extension === 'sass' || $extension === 'scss') {
            $fileName = $slugBase . "." . $extension;
            file_put_contents($path . $fileName, "/* Fichier " . strtoupper($extension) . " généré */");
        }
        elseif ($extension === 'md') {
            $fileName = $slugBase . ".md";
            file_put_contents($path . $fileName, "# " . $cleanName);
        }
        elseif ($extension === 'sql') {
            $fileName = $slugBase . ".sql";
            file_put_contents($path . $fileName, "-- Script SQL pour " . $cleanName);
        }
        else {
            if (strtolower($cleanName) === 'index') {
                $fileName = "index.php";
                $html = $this->prepareStandardHTML($siteName, $relPath, $cleanName);
            } elseif (strtolower($cleanName) === 'sitemap' || strtolower($cleanName) === 'plan') {
                $fileName = $slugBase . ".php";
                $html = $this->prepareSitemapHTML($siteName, $level, $treeHtml, $siteSlug, $relPath);
            } else {
                $fileName = $slugBase . ".php";
                $html = $this->prepareStandardHTML($siteName, $relPath, $cleanName);
            }
            file_put_contents($path . $fileName, $html);
        }
    }

    private function prepareStandardHTML($siteName, $relPath, $pageName) {
        return "<!DOCTYPE html>\n<html lang='fr'>\n<head>\n<meta charset='UTF-8'>\n<title>" . htmlspecialchars($pageName) . " - " . htmlspecialchars($siteName) . "</title>\n"
             . "<link rel='stylesheet' href='{$relPath}assets/css/styles.css'>\n"
             . "</head>\n<body>\n"
             . "<header>\n    <h1>Bienvenue sur " . htmlspecialchars($siteName) . "</h1>\n    <p>Page : " . htmlspecialchars($pageName) . "</p>\n</header>\n"
             . "<main>\n    <h2>Contenu de la page</h2>\n    <p>Votre site est prêt à être personnalisé.</p>\n"
             . "    <div class='test-css-ok'>Si tu vois ce pavé rose flash avec des bordures noires, c'est que le CSS fonctionne parfaitement !</div>\n"
             . "</main>\n"
             . "</body>\n</html>";
    }

    private function prepareSitemapHTML($siteName, $level, $treeHtml, $siteSlug, $relPath) {
        $adminPath = $relPath . "../../admin.php";
        $downloadPath = $relPath . "../../admin.php?download=" . $siteSlug;

        $bodyContent = "<div style='margin-bottom: 20px;'>"
             . "<a href='{$adminPath}' style='display: inline-block; margin-right: 10px; padding: 8px 15px; background: #333; color: #f39c12; text-decoration: none; border-radius: 4px; font-weight: bold;'>← Retour Admin</a>"
             . "<a href='{$downloadPath}' style='display: inline-block; padding: 8px 15px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>📦 Télécharger le projet (.zip)</a>"
             . "</div>\n"
             . "<h1>Plan du site : " . htmlspecialchars($siteName) . "</h1>\n<p>" . $treeHtml . "</p>";
        
        return "<!DOCTYPE html>\n<html lang='fr'>\n<head>\n<meta charset='UTF-8'>\n<title>Plan du site - " . htmlspecialchars($siteName) . "</title>\n"
             . "<link rel='stylesheet' href='{$relPath}assets/css/styles.css'>\n"
             . "<style>body { line-height: 1.6; max-width: 800px; margin: 40px auto; padding: 20px; font-family: sans-serif; background: #fff; }</style>\n"
             . "</head>\n<body>\n"
             . $bodyContent
             . "\n</body>\n</html>";
    }

    private function slugify($text) {
        if (is_array($text)) { return "folder"; } 
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}