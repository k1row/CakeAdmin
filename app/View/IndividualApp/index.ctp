<html>
  <head>
    <titile>Individual Advertiser</titile>
  </head>
  <body>
  	<!-- sidebar -->
    <?php echo $this->element('menu'); ?>

    <div>campaign_id : [<?php echo $campaign_id; ?>]</div>

    <div id="content">
      <table>
          <tr>
            <td>target_date</td>
            <td>advertiser_id</td>
            <td>click_num</td>
            <td>install_num</td>
            <td>CVR</td>
            <td>CSV</td>
            <td>updated</td>
          </tr>

        <?php foreach ($datas as $data): ?>
          <tr>
            <td><?php echo $data['AdminAnalyzeCampaign']['target_date']; ?></td>
            <td><?php echo $data['AdminAnalyzeCampaign']['advertiser_id']; ?></td>
            <td><?php echo $data['AdminAnalyzeCampaign']['click_num']; ?></td>
            <td><?php echo $data['AdminAnalyzeCampaign']['install_num']; ?></td>

            <td>
              <?php if ($data['AdminAnalyzeCampaign']['cvr'] > 1) : ?>
                <div style="color:red; font-size:20pt;"><?php echo $data['AdminAnalyzeCampaign']['cvr']; ?></div>
              <?php else : ?>
                <?php echo $data['AdminAnalyzeCampaign']['cvr']; ?>
              <?php endif; ?>
            </td>

            <td><a href="/IndividualAppCSVDownload?cid=<?php echo $campaign_id; ?>&tdate=<?php echo $data['AdminAnalyzeCampaign']['target_date']; ?>">Get detail</td>
            <td><?php echo $data['AdminAnalyzeCampaign']['modified']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <?php echo $this->element('pager'); ?>

  </body>
</html>
