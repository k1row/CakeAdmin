    <?php
        //print $this->Paginator->counter('{:count}件中{:start}-{:end}件({:pages}ページ中{:page}ページ)');
        if($this->Paginator->hasPrev()) print $this->Paginator->prev('≪' , array('class'=>'block'));
        print $this->Paginator->numbers(array(
        'class'=>'block',
        'modules' => 6 ,
        'first'=>2,
        'last'=>2,
        'currentClass'=>'red',
        'separator'=>null
        ));
        if($this->Paginator->hasNext()) print $this->Paginator->next(' ≫' , array('class'=>'block'));
    ?>
