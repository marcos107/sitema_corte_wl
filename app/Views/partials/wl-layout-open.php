<?php $wlEmbedMode = !empty($embed_mode); ?>
<!doctype html>
<html lang="pt-br" data-layout="vertical" data-layout-style="default" data-layout-position="fixed" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-layout-width="fluid">
<head>
    <?= $this->include('partials/wl-head') ?>
</head>
<body>
    <div id="layout-wrapper">
        <?php if (!$wlEmbedMode) { ?>
            <?= $this->include('partials/wl-topbar') ?>
            <?= $this->include('partials/wl-sidebar') ?>
        <?php } ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php if (!$wlEmbedMode) { ?>
                    <div class="wl-page-title">
                        <h4><?= isset($functionType) && $functionType ? esc($functionType) : esc($titulo ?? 'Painel') ?></h4>
                    </div>
                    <?php } ?>
