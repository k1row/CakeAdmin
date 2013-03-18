<html>
  <head>
    <titile>Individual Advertiser</titile>
  </head>
  <body>
  	<!-- sidebar -->
    <?php echo $this->element('menu'); ?>

    <div>publisher_id &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: [<?php echo $publisher_id; ?>]</div>
    <div>publisher_name : [<?php echo $publisher_name; ?>]</div>

    <div id="content">
      <table>
          <tr>
            <td>advertiser_id</td>
            <td>campaign_id</td>
            <td>campaign_name</td>
            <td>expense</td>
            <td>cpi</td>
            <td>install_num</td>
            <td>os</td>
            <td>icentive_type</td>
            <td>updated</td>
          </tr>

        <?php foreach ($datas as $data): ?>
          <tr>
            <td><?php echo $data['AdminAnalyzePublisher']['advertiser_id']; ?></td>
            <td><?php echo $data['AdminAnalyzePublisher']['campaign_id']; ?></td>
            <td><?php echo $data['AdminAnalyzePublisher']['campaign_name']; ?></td>
            <td><?php echo $data['AdminAnalyzePublisher']['expense']; ?></td>
            <td><?php echo $data['AdminAnalyzePublisher']['cpi']; ?></td>
            <td><?php echo $data['AdminAnalyzePublisher']['install_num']; ?></td>

            <td>
              <?php if ($data['AdminAnalyzePublisher']['ios'] == 1 && $data['AdminAnalyzePublisher']['android'] == 0) : ?>
                iOS
              <?php elseif ($data['AdminAnalyzePublisher']['ios'] == 0 && $data['AdminAnalyzePublisher']['android'] == 1) : ?>
                Android
              <?php elseif ($data['AdminAnalyzePublisher']['ios'] == 1 && $data['AdminAnalyzePublisher']['android'] == 1) : ?>
                iOS/Android
              <?php elseif (isset($data['AdminAnalyzePublisher']['ios']) && isset($data['AdminAnalyzePublisher']['android'])) : ?>
                <div style="color:red; font-size:10pt;">illegal data<br />(ios is null and android is null)</div>
              <?php elseif ($data['AdminAnalyzePublisher']['ios'] == 0 && $data['AdminAnalyzePublisher']['android'] == 0) : ?>
                <div style="color:red; font-size:10pt;">illegal data<br />(ios = 0 and android = 0)</div>
              <?php endif; ?>
            </td>

            <td>
              <?php if ($data['AdminAnalyzePublisher']['incentivized'] == 1 && $data['AdminAnalyzePublisher']['non_incentivized'] == 0) : ?>
                Incentivized
              <?php elseif ($data['AdminAnalyzePublisher']['incentivized'] == 0 && $data['AdminAnalyzePublisher']['non_incentivized'] == 1) : ?>
                Non-Incentivized
              <?php elseif ($data['AdminAnalyzePublisher']['incentivized'] == 1 && $data['AdminAnalyzePublisher']['non_incentivized'] == 1) : ?>
                Incentivized/Non-Incentivized
              <?php else : ?>
                <div style="color:red; font-size:10pt;">illegal data<br />(incentivized = 0 and non_incentivized = 0)</div>
              <?php endif; ?>
            </td>

            <td><?php echo $data['AdminAnalyzePublisher']['modified']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <?php echo $this->element('pager'); ?>

  </body>
</html>
