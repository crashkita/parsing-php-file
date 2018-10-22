<?php
namespace app\core;

/**
 * Class Controller
 * @package app\core
 */
class Controller
{
    /**
     * @var string Controller id
     */
    private $_id;

    /**
     * @var string Layout file
     */
    public $layout = 'index';

    /**
     * Controller constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->_id = $id;
    }

    /**
     * @param $view
     * @param array $params
     * @return false|string
     */
    public function render($view, $params = [])
    {
        $filePath = $this->getViewFile($view, $this->_id);
        $content = $this->renderFile($filePath, $params);
        $layoutPath = $this->getViewFile($this->layout, 'layout');
        return $this->renderFile($layoutPath, ['content' => $content]);
    }

    /**
     * @param $view
     * @param $subDir
     * @return string
     */
    protected function getViewFile($view, $subDir): string
    {
        return __DIR__ .DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . $view . '.php';
    }

    /**
     * @param $filePath
     * @param $params
     * @return false|string
     */
    protected function renderFile($filePath, $params)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require $filePath;
        return ob_get_clean();
    }
}