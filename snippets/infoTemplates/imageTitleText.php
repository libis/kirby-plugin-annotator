<div class="annotator-title-image-textblock">
  <?php if ($data->image()->toFiles()->isNotEmpty() && $data->image()->toFiles()->count() > 0): ?> 
    <?php $mediaFiles = $data->image()->toFiles() ?>
    <div class="annotator-title-image-textblock__image-section image-section">
      <div class="image-section__image-wrapper image-wrapper">
        <?php foreach($mediaFiles as $media): ?>      
          <picture>
            <source
              srcset="<?= $media->srcset('') ?>"
              type="image/webp"
            >
            <img 
              src="<?= $media->resize(250)->url() ?>" 
              alt="<?= $media->alt() ?>"
              style="object-position: <?= $media->focus()->or('50% 50%') ?>"
            >
          </picture>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  <div class="annotator-title-image-textblock__text-wrapper text-wrapper">
    <h3 class="text-wrapper__annotator-title annotator-title h3"><?= $data->title() ?></h3>
    <p class="text-wrapper__annotator-text annotator-text body-text"><?= $data->text() ?></p>
  </div>
</div>