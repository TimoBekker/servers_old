<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Вход в систему';
// $this->params['breadcrumbs'][] = $this->title;

/* подписываемая контрольная строка */
$codeToSign = base64_encode(uniqid());
echo '<input type="hidden" id="signstring" value="'.$codeToSign.'"/>';
echo '<input type="hidden" name="method" id="method" value="">';
/*----------------------------------*/
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Информация по входу по ЭЦП' ?></h4>
            </div>
            <div class="modal-body" id="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="site-login">
    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?php // <p>Тестовый вход на время разработки. В дальнейшем будет по ЭЦП:</p> ?>
    <div class="row">
        <div class="col-lg-5" style="background: rgb(240, 240, 240); border:1px solid rgb(199, 188, 188); border-radius:5px">
           <br>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?php //var_dump($model);exit; ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?php // = $form->field($model, 'rememberMe')->checkbox() ?>
                <?php /*
                <div style="color:#999;margin:1em 0">
                    If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
                </div>
                */?>
                <div class="form-group">
                    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    <?= Html::button('Войти по ЭЦП', ['class' => 'btn btn-success', 'id' => 'login-electronic-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$script = <<< JS
function signEP() {
    var signString = document.getElementById('signstring').value;
    var soapMessage = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">'
                        +'<soapenv:Header/><soapenv:Body><tem:SignMessage><tem:base64Data>'
                        + signString
                        +'</tem:base64Data></tem:SignMessage></soapenv:Body></soapenv:Envelope>';
    var headers = ["SOAPAction", "http://tempuri.org/IXmlSigningService/SignMessage", "Content-Type", "text/xml"];
    var url = "http://localhost:30175/XmlSigningService/api/SignString";
    var method = "POST";
    var body = '{ "base64Data" : \"' + signString + '\" }';
    var xhr = new XMLHttpRequest();
    if ("withCredentials" in xhr) {
        // all modern browsers (IE 10+, Chrome, etc.)
        xhr.open(method, url, true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4) {
                var obj = JSON.parse(xhr.responseText);
                if (obj.SignStringResult != null) {
                    //alert("Response of Echo: " + obj.SignMessageResult.SignedXml);
                    var msg = obj.SignStringResult.SignedXml;
                    if (msg.indexOf("Issuer certificate not found on trusted root certificate store") > 0) {
                        alert("Ошибка входа с электронной подписью\\r\\n\
                               На вашем компьютере корневой сертификат издателя не установлен в хранилище\
                               доверенных корневых центров сертификации локального компьютера\\r\\n\
                               Сообщение: " + obj);
                    } else {
                        // все норм
                        // alert(msg);
                        $.post('', {signString:signString, dataString:msg}, function(data, textStatus, xhr) {
                            $('#loginform-username').next().text(data);
                            $('#loginform-username').parent().addClass('has-error');
                        });
                    }
                } else {
                    if (obj.FaultMessage == null) {
                        alert("Ошибка входа с электронной подписью\\r\\nСообщение: " + obj);
                    } else {
                        alert("Ошибка входа с электронной подписью\\r\\nСообщение: " + obj.FaultMessage);
                    }
                }
            }
        };
        xhr.onError = function (error) {
            var obj = JSON.parse(xhr.responseText);
            alert("Ошибка входа с электронной подписью\\r\\n" + obj.FaultMessage);
        }
        xhr.send(body);
        //document.getElementById("LoginForm_password").value = xhr.responseText.text.substr(1);
    }
}
JS;
$this->registerJs($script, yii\web\View::POS_HEAD);
$script = <<< JS
$(document).on('click', '#login-electronic-button', function(event) {
    event.preventDefault();
    signEP();
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);

