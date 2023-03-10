<?php if($categoryintegrations):?>
<?php foreach ($categoryintegrations as $category => $integrations) : ?>
<div class="col-md-12">
    <h4><?php echo $category ?></h4>
<?php foreach ($integrations as $integration) : ?>
    <div class="col-md-3" style="padding-right: 0;">
    <?php if($integration['targetUrl']==''): ?>
        <a href="#">
    <?php else: ?>
        <a href="<?php echo admin_url($integration['targetUrl']) ?>">
    <?php endif ; ?>
        <div class="integration">
            <?php if($integration['label']): ?>
            <div class="ribbon warning"><span><?php echo $integration['label']?></span></div>
            <?php endif; ?>
            <div class="row">
                <div class="col-xs-4"><img class="integration-logo" src="<?php echo base_url('assets/images/integrationslogo/' . $integration['logo']) ?>" alt=""></div>
                <div class="col-xs-8">
                    <div class="integration-title">
                        <h4><?php echo $integration['name'] ?></h4>
                    </div>
                    <div class="integration-description">
                        <p class="text-muted"><?php echo $integration['description'] ?></p>
                    </div>
                    <?php if($integration['tags']): ?>
                        <div class="integration-tags">
                        <?php foreach($integration['tags'] as $tag): ?>
                            <a class="badge badge-primary text-uppercase"><?php echo $tag ?></a>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </a>
    </div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
<?php else: ?>
    <div class="col-xs-12">
        <p class="text-muted text-center">No integrations found</p>
    </div>
<?php endif; ?>