<html>
  <head>
    <titile>Main Advertiser</titile>
  </head>
  <body>
  	<!-- sidebar -->
    <?php echo $this->element('menu'); ?>

    <div id="content">
      <table>
          <tr>
            <td>id</td>
            <td>company_name</td>
            <td>owner_name</td>
            <td>owner_email_address</td>
            <td>active_campaign</td>
          </tr>

        <?php foreach ($datas as $data): ?>
          <tr>
            <td><?php echo $data['id']; ?></td>
            <td><a href="IndividualAdvertiser?aid=<?php echo $data['id']; ?>&name=<?php echo $data['owner_name']; ?>"><?php echo $data['company_name']; ?></a></td>
            <td><?php echo $data['owner_name']; ?></td>
            <td><?php echo $data['owner_email_address']; ?></td>

            <td>
              <?php if ($data['enable_campaign_num'] > 0) : ?>
                <div style="color:blue; font-size:10pt;">YES</div>
              <?php else : ?>
                <div style="color:black; font-size:10pt;">NO</div>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

    <?php echo $this->element('pager'); ?>
    </div>
  </body>
</html>
