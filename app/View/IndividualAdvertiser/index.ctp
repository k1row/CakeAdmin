<html>
  <head>
    <titile>Individual Advertiser</titile>
  </head>
  <body>
  	<!-- sidebar -->
    <?php echo $this->element('menu'); ?>

    <div>[<?php echo $advertiser_name; ?>]</div>

    <div id="content">
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
            <td><a href="individualapp?cid=<?php echo $data['CampaignMaster']['id']; ?>"><?php echo $data['CampaignMaster']['id']; ?></a></td>
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

    <?php echo $this->element('pager'); ?>

  </body>
</html>
