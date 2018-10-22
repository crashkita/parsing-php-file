<?php

namespace app\controllers;

use app\components\InterfaceGenerator;
use app\components\PhpLoader;
use app\core\App;
use app\core\Controller;
use app\core\Request;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        /* @var $request Request */
        $request = App::getInstance()->getRequest();
        $fileInfo = $request->getFileInfoByName('file');
        $content = '';
        if (!empty($fileInfo)) {
            $filePath = $fileInfo['tmp_name'];
            $loader = new PhpLoader();
            $phpInfo = $loader->parsingTokens($loader->getTokens($filePath));
            $generator = new InterfaceGenerator($loader);
            $content = $generator->getContentInterface();

            $content = htmlspecialchars($content);
            $content = str_replace("\n", '<br>', $content);
            $content = preg_replace('/\s/', '&nbsp', $content);
        }

        return $this->render('index', ['fileContent' => $content]);
    }
}