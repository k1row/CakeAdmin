<html>
  <head>
    <title>Add Advertiser</title>
  </head>
  <body>

  <div id="contents">
    <div id="sub">
      <div class="inner">
      	<!-- sidebar -->
        <?php echo $this->element('menu'); ?>
      </div>
    </div>
    <div id="main">
      <div class="inner">
        <p><h2>Add New Advertiser</h2></p>
        <?php
          echo $this->Form->create('AdvertiserMaster');

          echo $this->Form->input('company_name');
          echo $this->Form->input('owner_name');
          echo $this->Form->input('owner_email_address');

          echo $this->Form->end('submit');
        ?>

      </div>
    </div>
  </div>
<!--
  <div id="content">
    <?php echo $this->element('pager'); ?>
  </div>
-->
  </body>
</html>
