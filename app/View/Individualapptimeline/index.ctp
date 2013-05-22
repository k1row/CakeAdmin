<html>
  <head>
    <title>Individual App Timeline</title>
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
        <p><h2>Individual App Timeline</h2></p>

        <p>
          <div><span style="color:blue;font-size:medium">appsigid</span></div>
          <div>&nbsp;&nbsp;[<?php echo $campaignmaster[0]['CampaignMaster']['id']; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">name</span></div>
          <div>&nbsp;&nbsp;[<?php echo $campaignmaster[0]['CampaignMaster']['name']; ?>]</div>
        </p>

        <table width="20%">
          <tr>
            <td>Timeline</td>
            <td>install_num</td>
          </tr>

          <?php foreach ($datas as $key => &$value): ?>
            <tr>
              <td><?php echo $key; ?></td>
              <td><?php echo $value; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
  </body>
</html>
