<?php

namespace bupy7\gallery\manager;


use Yii;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * Backend controller for GalleryManager widget.
 * Provides following features:
 *  - Image removal
 *  - Image upload/Multiple upload
 *  - Arrange images in gallery
 *  - Changing name/description associated with image
 *
 * @author Bogdan Savluk <savluk.bogdan@gmail.com>
 * @author Vasilij Belosludcev http://mihaly4.ru
 */
class GalleryManagerAction extends Action
{

    /**
     * @var array Mapping between types and model class names
     */
    public $types = [];


    private $type;
    private $behaviorName;
    private $galleryId;

    /** @var  ActiveRecord */
    private $owner;
    /** @var  GalleryBehavior */
    private $behavior;


    public function run($action)
    {
        $this->type = Yii::$app->request->get('type');
        $this->behaviorName = Yii::$app->request->get('behaviorName');
        $this->galleryId = Yii::$app->request->get('galleryId');

        $this->owner = $this->types[$this->type]->andWhere(['id' => $this->galleryId])->one();
        if ($this->owner == null) {
            throw new NotFoundHttpException('Gallery not found.');
        }
        $this->behavior = $this->owner->getBehavior($this->behaviorName);

        switch ($action) {
            case 'delete':
                return $this->actionDelete(Yii::$app->request->post('id'));
               
            case 'ajaxUpload':
                return $this->actionAjaxUpload();
               
            case 'changeData':
                return $this->actionChangeData(Yii::$app->request->post('photo'));
               
            case 'order':
                return $this->actionOrder(Yii::$app->request->post('order'));

            default:
                throw new BadRequestHttpException('Action do not exists');
        }
    }

    /**
     * Removes image with ids specified in post request.
     * On success returns 'OK'
     *
     * @param $ids
     *
     * @throws HttpException
     * @return string
     */
    private function actionDelete($ids)
    {

        $this->behavior->deleteImages($ids);

        return 'OK';
    }

    /**
     * Method to handle file upload thought XHR2
     * On success returns JSON object with image info.
     *
     * @param $gallery_id string Gallery Id to upload images
     *
     * @return string
     * @throws HttpException
     */
    public function actionAjaxUpload()
    {

        $imageFile = UploadedFile::getInstanceByName('image');

        $fileName = $imageFile->tempName;
        $image = $this->behavior->addImage($fileName);

        // not "application/json", because  IE8 trying to save response as a file

        Yii::$app->response->headers->set('Content-Type', 'text/html');

        return Json::encode(
            array(
                'id' => $image->id,
                'rank' => $image->rank,
                'name' => (string)$image->name,
                'description' => (string)$image->description,
                'preview' => $image->getUrl('preview'),
            )
        );
    }

    /**
     * Saves images order according to request.
     * Variable $_POST['order'] - new arrange of image ids, to be saved
     * @throws HttpException
     */
    public function actionOrder($order)
    {
        if (count($order) == 0) {
            throw new BadRequestHttpException('No data, to save');
        }
        $res = $this->behavior->arrange($order);

        return Json::encode($res);

    }

    /**
     * Method to update images name/description via AJAX.
     * On success returns JSON array od objects with new image info.
     *
     * @param $imagesData
     *
     * @throws HttpException
     * @return string
     */
    public function actionChangeData($imagesData)
    {
        if (count($imagesData) == 0) {
            throw new BadRequestHttpException('Nothing to save');
        }
        $images = $this->behavior->updateImagesData($imagesData);
        $resp = array();
        foreach ($images as $model) {
            $resp[] = array(
                'id' => $model->id,
                'rank' => $model->rank,
                'name' => (string)$model->name,
                'description' => (string)$model->description,
                'preview' => $model->getUrl('preview'),
            );
        }

        return Json::encode($resp);
    }
}
