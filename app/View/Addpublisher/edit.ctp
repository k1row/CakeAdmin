<html>
  <head>
    <title>Edit Publisher</title>
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
        <p><h2>Edit Publisher</h2></p>
        <?php
          echo $this->Form->create('PublisherMaster');

          echo $this->Form->input('owner_name');
          echo $this->Form->input('owner_email_address');
          echo $this->Form->input('sub_email_address');
          echo $this->Form->input('url');
        ?>

        <div style="font-size:8pt;">
          <?php echo $this->Form->input('enable', array('type' => 'radio', 'options' => array('1' => 'ON', '0' => 'OFF'))); ?>
          <?php echo $this->Form->input('ios', array('type' => 'radio', 'options' => array('1' => 'ON', '0' => 'OFF'))); ?>
          <?php echo $this->Form->input('android', array('type' => 'radio', 'options' => array('1' => 'ON', '0' => 'OFF'))); ?>
          <?php echo $this->Form->input('incentivized', array('type' => 'radio', 'options' => array('1' => 'ON', '0' => 'OFF'))); ?>
          <?php echo $this->Form->input('non_incentivized', array('type' => 'radio', 'options' => array('1' => 'ON', '0' => 'OFF'))); ?>
          <?php echo $this->Form->input('redirect', array('type' => 'radio', 'options' => array('1' => 'ON', '0' => 'OFF'))); ?>
          <?php echo $this->Form->input('postback', array('type' => 'radio', 'options' => array('1' => 'ON', '0' => 'OFF'))); ?>
        </div>

        <?php echo $this->Form->end('submit'); ?>
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
