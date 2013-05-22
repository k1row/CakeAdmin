<html>
  <head>
    <title>Main Publisher</title>
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
        <p><h2>Publisher List</h2></p>
        <table>
            <tr>
              <td>id</td>
              <td>owner_name</td>
              <td>owner_email_address</td>
              <td>sub_email_address</td>
              <td>enable</td>
              <td>os</td>
              <td>incentive_type</td>
              <td>redirect</td>
              <td>postback</td>
            </tr>
  
          <?php foreach ($datas as $data): ?>
            <tr>
              <td><?php echo $data['PublisherMaster']['id']; ?></td>
              <td><a href="Individualpublisher?pid=<?php echo $data['PublisherMaster']['id']; ?>&name=<?php echo $data['PublisherMaster']['owner_name']; ?>"><?php echo $data['PublisherMaster']['owner_name']; ?></a></td>
              <td><?php echo $data['PublisherMaster']['owner_email_address']; ?></td>
              <td><?php echo $data['PublisherMaster']['sub_email_address']; ?></td>
  
              <td>
                <?php if ($data['PublisherMaster']['enable'] == 1) : ?>
                  <div style="color:blue; font-size:10pt;">YES</div>
                <?php else : ?>
                  <div style="color:black; font-size:10pt;">NO</div>
                <?php endif; ?>
              </td>
  
              <td>
                <?php if ($data['PublisherMaster']['ios'] == 1 && $data['PublisherMaster']['android'] == 0) : ?>
                  iOS
                <?php elseif ($data['PublisherMaster']['ios'] == 0 && $data['PublisherMaster']['android'] == 1) : ?>
                  Android
                <?php elseif ($data['PublisherMaster']['ios'] == 1 && $data['PublisherMaster']['android'] == 1) : ?>
                  iOS/Android
                <?php elseif (isset($data['PublisherMaster']['ios']) && isset($data['PublisherMaster']['android'])) : ?>
                  <div style="color:red; font-size:10pt;">illegal data<br />(ios is null and android is null)</div>
                <?php elseif ($data['PublisherMaster']['ios'] == 0 && $data['PublisherMaster']['android'] == 0) : ?>
                  <div style="color:red; font-size:10pt;">illegal data<br />(ios = 0 and android = 0)</div>
                <?php endif; ?>
              </td>
  
              <td>
                <?php if ($data['PublisherMaster']['incentivized'] == 1 && $data['PublisherMaster']['non_incentivized'] == 0) : ?>
                  Incentivized
                <?php elseif ($data['PublisherMaster']['incentivized'] == 0 && $data['PublisherMaster']['non_incentivized'] == 1) : ?>
                  Non-Incentivized
                <?php elseif ($data['PublisherMaster']['incentivized'] == 1 && $data['PublisherMaster']['non_incentivized'] == 1) : ?>
                  Incentivized/Non-Incentivized
                <?php else : ?>
                  <div style="color:red; font-size:10pt;">illegal data<br />(incentivized = 0 and non_incentivized = 0)</div>
                <?php endif; ?>
              </td>
  
              <td>
                <?php if ($data['PublisherMaster']['redirect'] == 1) : ?>
                  <div style="color:blue; font-size:10pt;">YES</div>
                <?php else : ?>
                  <div style="color:black; font-size:10pt;">NO</div>
                <?php endif; ?>
              </td>
  
              <td>
                <?php if ($data['PublisherMaster']['postback'] == 1) : ?>
                  <div style="color:blue; font-size:10pt;">YES</div>
                <?php else : ?>
                  <div style="color:black; font-size:10pt;">NO</div>
                <?php endif; ?>
              </td>
  
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
  <?php echo $this->element('pager'); ?>
  </body>
</html>
