<html>
  <head>
    <title>Individual App</title>
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
        <p><h2>Indivudual App Detail</h2></p>

        <p>
          <div><span style="color:blue;font-size:medium">appsigid</span></div>
          <div>&nbsp;&nbsp;[<?php echo $campaignmaster[0]['CampaignMaster']['id']; ?>]</div>
        </p>
        <p>
          <div><span style="color:blue;font-size:medium">name</span></div>
          <div>&nbsp;&nbsp;[<?php echo $campaignmaster[0]['CampaignMaster']['name']; ?>]</div>
        </p>

        <p>
          <div><span style="color:blue;font-size:medium">Get detail data for CSV</span></div>
          <?php
            function optionLoop($start, $end, $value = null)
            {
            	for($i = $start; $i <= $end; $i++)
              {
            		if(isset($value) && $value == $i)
                {
            			echo "<option value=\"{$i}\" selected=\"selected\">{$i}</option>";
            		}
                else
                {
            			echo "<option value=\"{$i}\">{$i}</option>";
            		}
            	}
            }
          ?>

          <form id="form1" name="form1" method="get" action="/Individualappcsvdownload" class="form-inline">
            <input type="hidden" name="cid" value="<?php echo $appsigid; ?>">
            <div>
              <select name="start_year">
                <?php $startY = substr($campaignmaster[0]['CampaignMaster']['begin_time'], 0, 4); ?>
                <?php $endY = substr($campaignmaster[0]['CampaignMaster']['end_time'], 0, 4); ?>
                <?php optionLoop($startY, $endY, date('Y')); ?>
              </select>
              &nbsp;/&nbsp;
              <select name="start_month">
                <?php optionLoop('1', '12', date('m'));?>/
              </select>
              &nbsp;/&nbsp;
              <select name="start_day">
                <?php optionLoop('1', '31', date('d'));?>
              </select>
              &nbsp;&nbsp;-&nbsp;&nbsp;
              <select name="end_year">
                <?php $startY = substr($campaignmaster[0]['CampaignMaster']['begin_time'], 0, 4); ?>
                <?php $endY = substr($campaignmaster[0]['CampaignMaster']['end_time'], 0, 4); ?>
                <?php optionLoop($startY, $endY, date('Y')); ?>
              </select>
              &nbsp;/&nbsp;
              <select name="end_month">
                <?php optionLoop('1', '12', date('m'));?>/
              </select>
              &nbsp;/&nbsp;
              <select name="end_day">
                <?php optionLoop('1', '31', date('d'));?>
              </select>
            </div>
            <div>
              <input name="dl_type" type="radio" value="All Day" checked>All Day
              <br>
              <input name="dl_type" type="radio" value="Timeline">Timeline
              <select name="time">
                <?php optionLoop('0', '24', date('H'));?>/
              </select>
            </div>

            <div><input type="submit" value="Download"></div>
          </form>
        </p>
        <br />

        <p><div><span style="color:blue;font-size:medium">Total</span></div></p>
        <table>
          <tr>
            <td>total campaign date</td>
            <td>advertiser_id</td>
            <td>click_num</td>
            <td>install_num</td>
            <td>CVR</td>
            <td>updated</td>
          </tr>

          <?php foreach ($datas as $data): ?>
            <tr>
              <td><?php echo $data['AdminAnalyzeCampaign']['begin_time']; ?> => <?php echo $data['AdminAnalyzeCampaign']['end_time']; ?></td>
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

              <td><?php echo $data['AdminAnalyzeCampaign']['modified']; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>

        <br>
        <p><div><span style="color:blue;font-size:medium">Daily</span></div></p>
        <table>
          <tr>
            <td>tareget_date</td>
            <td>click_num</td>
            <td>install_num</td>
            <td>CVR</td>
            <td>updated</td>
          </tr>

          <?php foreach ($dailydatas as $data): ?>
            <tr>
              <td><?php echo $data['AdminAnalyzeCampaignPerDay']['target_date']; ?></td>
              <td><?php echo $data['AdminAnalyzeCampaignPerDay']['click_num']; ?></td>
              <td><a href="/Individualapptimeline?cid=<?php echo $appsigid; ?>&target_date=<?php echo $data['AdminAnalyzeCampaignPerDay']['target_date']; ?>"><?php echo $data['AdminAnalyzeCampaignPerDay']['install_num']; ?></td>

              <td>
                <?php if ($data['AdminAnalyzeCampaignPerDay']['cvr'] > 1) : ?>
                  <div style="color:red; font-size:20pt;"><?php echo $data['AdminAnalyzeCampaignPerDay']['cvr']; ?></div>
                <?php else : ?>
                  <?php echo $data['AdminAnalyzeCampaignPerDay']['cvr']; ?>
                <?php endif; ?>
              </td>

              <td><?php echo $data['AdminAnalyzeCampaignPerDay']['modified']; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
  <?php echo $this->element('pager'); ?>
  </body>
</html>
