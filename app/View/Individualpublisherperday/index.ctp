<html>
  <head>
    <title>Individual Advertiser</title>
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
        <p><h2>Indivudual Publisher Detail Per Day</h2></p>

        <p>
          <div><span style="color:blue;font-size:medium">publisher_id</span></div>
          <div>&nbsp;&nbsp;[<?php echo $publisher_id; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">publisher_name</div>
          <div>&nbsp;&nbsp;[<?php echo $publisher_name; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">appsigid</div>
          <div>&nbsp;&nbsp;[<?php echo $campaignmaster[0]['CampaignMaster']['id']; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">campaign_name</div>
          <div>&nbsp;&nbsp;[<?php echo $campaignmaster[0]['CampaignMaster']['name']; ?>]</div>
        </p>
        <br />

        <table>
          <tr>
            <td>target_date</td>
            <td>install_num</td>
            <td>updated</td>
          </tr>

          <?php foreach ($datas as $data): ?>
            <tr>
              <td><?php echo $data['AdminAnalyzePublisherPerDay']['target_date']; ?></td>
              <td><?php echo $data['AdminAnalyzePublisherPerDay']['install_num']; ?></td>
              <td><?php echo $data['AdminAnalyzePublisherPerDay']['modified']; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>

  <?php echo $this->element('pager'); ?>

  </body>
</html>
