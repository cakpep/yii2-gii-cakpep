<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/* developed by cakpep team contact us on febrimaschut@gmail.com */

class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    public function actionDetailData(<?= $actionParams ?>)
    {
        return $this->renderAjax('detail', [
            'model' => $this->findModel(<?= $actionParams ?>),
        ]);
    }

    public function actionAddData()
    {
        $model = new <?= $modelClass ?>();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $model->created_by=Yii::$app->user->id;
                $model->created_date=date('Y-m-d H:i:s');

            if($model->save()){
                
                  $res = array(
                        'message' => 'Data Berhasil Di Simpan.',
                        'alert'=> 'success',
                        'proses'=>'save',
                        'success' => true,
                    );
            }else{
                $res = array(
                    'message' => 'Data Gagal Di Simpan.',
                    'alert'=> 'error',
                    'proses'=>'save',
                    'success' => false,
                );         
            }
            
            return $res;
            \Yii::$app->end();
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

   

    public function actionUpdateData(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $model->updated_by=Yii::$app->user->id;
                $model->updated_date=date('Y-m-d H:i:s');

            if($model->save()){
                
                  $res = array(
                        'message' => 'Data Berhasil Di Update.',
                        'alert'=> 'success',
                        'proses'=> 'update',
                        'success' => true,
                    );

            }else{
                
                $res = array(
                    'message' => 'Data Gagal Di Update.',
                    'alert'=> 'error',
                    'proses'=>'update',
                    'success' => false,
                );  

            }
            
            return $res;
            \Yii::$app->end();

        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete(<?= $actionParams ?>)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if($this->findModel(<?= $actionParams ?>)->delete()){
                
                $res = array(
                    'message' => 'Data Berhasil Di Hapus.',
                    'alert'=> 'success',
                    'proses'=> 'delete',
                    'success' => true,
                );
        }else{
                
                $res = array(
                    'message' => 'Data Gagal Di Hapus.',
                    'alert'=> 'error',
                    'proses'=> 'delete',
                    'success' => false,
                );         
        }
            
        return $res;
        \Yii::$app->end();
    }

    public function actionDeleteAll(<?= $actionParams ?>)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $idx=explode(',', $id);

        foreach ($idx as $id) {
            $this->findModel(<?= $actionParams ?>)->delete();
            $a=1;
        }
            
        if($a){
                
                $res = array(
                    'message' => count($idx).' Data Berhasil Di Hapus.',
                    'alert'=> 'success',
                    'proses'=> 'delete',
                    'success' => true,
                );

        }else{
                
                $res = array(
                    'message' => 'Data Gagal Di Hapus.',
                    'alert'=> 'error',
                    'proses'=> 'delete',
                    'success' => false,
                );         

        }
            
        return $res;
        \Yii::$app->end();
    }

    
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
