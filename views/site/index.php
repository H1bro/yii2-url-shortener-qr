<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = 'Сервис коротких ссылок + QR';
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-light rounded-3 mt-4">
        <div class="container-fluid py-4">
            <h1 class="display-6">Сервис коротких ссылок + QR</h1>
            <p class="lead mb-4">Вставьте ссылку и получите короткий URL и QR-код без перезагрузки страницы.</p>

            <form id="shorten-form" action="<?= Url::to(['/site/create-short-url']) ?>" method="post" class="row g-2">
                <div class="col-md-9">
                    <input
                        type="url"
                        id="url-input"
                        name="url"
                        class="form-control form-control-lg"
                        placeholder="https://example.com/page"
                        required
                    >
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">OK</button>
                </div>
            </form>
        </div>
    </div>

    <div id="shortener-result" class="alert d-none" role="alert"></div>
    <div id="shortener-error" class="alert d-none" role="alert"></div>

    <div id="shortener-output" class="row align-items-center d-none">
        <div class="col-md-4 text-center mb-3 mb-md-0">
            <img id="qr-code-image" src="" alt="QR Code" class="img-fluid border rounded p-2 bg-white">
        </div>
        <div class="col-md-8">
            <h5>Короткая ссылка:</h5>
            <p class="mb-2">
                <a id="short-link" href="#" target="_blank" rel="noopener noreferrer"></a>
            </p>
            <p class="text-muted mb-0">Отсканируйте QR камерой телефона или перейдите по короткой ссылке.</p>
        </div>
    </div>
</div>
