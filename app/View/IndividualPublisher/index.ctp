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
        <p><h2>Indivudual Publisher Detail</h2></p>

        <p>
          <div><span style="color:blue;font-size:medium">publisher_id</span></div>
          <div>&nbsp;&nbsp;[<?php echo $publisher_id; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">publisher_name</div>
          <div>&nbsp;&nbsp;[<?php echo $publisher_name; ?>]</div>
        </p>
        <br />

        <table>
          <tr>
            <td>advertiser_id</td>
            <td>appsigid</td>
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
              <td><?php echo $data['AdminAnalyzePublisher']['appsigid']; ?></td>
              <td><?php echo $data['AdminAnalyzePublisher']['campaign_name']; ?></td>
              <td><?php echo $data['AdminAnalyzePublisher']['expense']; ?></td>
              <td><?php echo $data['AdminAnalyzePublisher']['cpi']; ?></td>
              <td><a href="/Individualpublisherperday?cid=<?php echo $data['AdminAnalyzePublisher']['appsigid']; ?>&pid=<?php echo $publisher_id; ?>&name=<?php echo $publisher_name; ?>"><?php echo $data['AdminAnalyzePublisher']['install_num']; ?></a></td>

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
    </div>
  </div>

  <?php echo $this->element('pager'); ?>

  </body>
</html>
