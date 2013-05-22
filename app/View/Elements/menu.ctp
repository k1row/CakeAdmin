<div align="center" style="color:#228b22; font-size:15pt;">Phoenix Dashbord</div>

<br />

<div>
  <ul>
    <div style="color:#1e90ff; font-size:13pt;">Advertiser</div>
    <li><a href="/Mainadvertiser">Top</a></li>
    <li><a href="/Addadvertiser/add">Add</a></li>
    <?php if (preg_match("/Individualadvertiser/", $this->Html->url('', true))) : ?>
      <li><a href="/Addadvertiser/edit/<?php echo $advertiser[0]['AdvertiserMaster']['id']; ?>">Edit this advertiser</a></li>
    <?php endif; ?>
  </ul>
</div>
<br />
<div>
  <ul>
    <div style="color:red; font-size:13pt;">Publisher</div>
    <li><a href="/Mainpublisher">Top</a></li>
    <li><a href="/Addpublisher/add">Add</a></li>
    <?php if (preg_match("/Individualpublisher/", $this->Html->url('', true))) : ?>
      <li><a href="/Addpublisher/edit/<?php echo $publisher_id; ?>">Edit this publisher</a></li>
    <?php endif; ?>
  </ul>
</div>
