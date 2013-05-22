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
        <p><h2>Indivudual Advertiser Detail</h2></p>

        <p>
          <div><span style="color:blue;font-size:medium">company_name</span></div>
          <div>&nbsp;&nbsp;[<?php echo $advertiser[0]['AdvertiserMaster']['company_name']; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">owner_name</div>
          <div>&nbsp;&nbsp;[<?php echo $advertiser[0]['AdvertiserMaster']['owner_name']; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">owner_email_address</span></div>
          <div>&nbsp;&nbsp;[<?php echo $advertiser[0]['AdvertiserMaster']['owner_email_address']; ?>]</div>
        </p>
        <br />

        <table>
            <tr>
              <td>campaign id</td>
              <td>name</td>
              <td>url</td>
              <td>device</td>
              <td>begin_time</td>
              <td>end_time</td>
              <td>click_campaign</td>
              <td>has_offers</td>
            </tr>

          <?php foreach ($datas as $data): ?>
            <tr>
              <td><a href="Individualapp?cid=<?php echo $data['CampaignMaster']['id']; ?>"><?php echo $data['CampaignMaster']['id']; ?></a></td>
              <td><?php echo $data['CampaignMaster']['name']; ?></td>
              <td><?php echo $data['CampaignMaster']['url']; ?></td>
              <td><?php echo $data['CampaignMaster']['device']; ?></td>
              <td><?php echo $data['CampaignMaster']['begin_time']; ?></td>
              <td><?php echo $data['CampaignMaster']['end_time']; ?></td>
              <td><?php echo $data['CampaignMaster']['click_campaign']; ?></td>
              <td><?php echo $data['CampaignMaster']['has_offers']; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>

      </div>
    </div>
  </div>

  <?php echo $this->element('pager'); ?>

  </body>
</html>
