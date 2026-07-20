<div class="block-type-<?= $block->type() ?> <?= $block->class() ?> <?= $block->marginbottom() != '' || $block->margintop() != '' ? 'custom-margin' : '' ?>" id="<?= $block->ids() ?>" style="<?= $block->margintop() != '' ? '--custom-margin-top:' . $block->margintop() . 'px;' : ''; ?>
<?= $block->marginbottom() != '' ? '--custom-margin-bottom:' . $block->marginbottom() . 'px;' : ''; ?>">
    <?php snippet("fields/annotator", ["annotator" => $block->annotator(), 'blockId' => $block->id()]) ?>
</div>