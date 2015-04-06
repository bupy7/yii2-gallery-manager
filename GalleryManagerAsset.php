<?php

namespace bupy7\gallery\manager;

use yii\web\AssetBundle;

class GalleryManagerAsset extends AssetBundle
{
    
    public $sourcePath = '@bupy7/gallery/manager/assets';
    public $js = [
        'jquery.iframe-transport.min.js',
        'jquery.gallery-manager.min.js',
    ];
    public $css = [
        'gallery-manager.min.css'
    ];
    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}
