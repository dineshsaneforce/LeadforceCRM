<style>
.search-tabs>li.active>a, .search-tabs>li.active>a:focus, .search-tabs>li.active>a:hover, .navbar-pills.search-tabs>li>a:focus, .navbar-pills.search-tabs>li>a:hover {
    border-bottom: 1px solid var(--theme-primary-light);
}

.search-tabs>li.active>a, .search-tabs>li.active>a:focus, .search-tabs>li.active>a:hover, .search-tabs>li>a:focus, .search-tabs>li>a:hover {
    border-bottom: 1px solid var(--theme-primary-light);
    color: var(--theme-primary-light);
}
.search-tabs-horizontal li.active a .badge, .search-tabs-horizontal li:hover a .badge {
    background-color: var(--theme-primary-light);
}
.nav-container.width350{
    left: unset;
    margin-left: 0px;
    width: 32vw;
    max-height: calc( 100vh - 60px);
    overflow-x: auto;
}
.search-tabs {
    padding-bottom: 0;
    margin-bottom: 25px;
    background: 0 0;
    border-radius: 1px;
    padding-left: 0;
    padding-right: 0;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
    overflow-y: hidden;
    display: flex;
}
</style>
<div class="nav-container">
  <ol class="nav search-tabs">
      <!-- <li class="active">
        <a class="search-tabs-a" data-toggle="tab" href="#THall">
          <i class="fa fa-th-large"></i> All (<span id="THcall">0</span>)
        </a>
      </li> -->
      <li class="active">
        <a class="search-tabs-a" data-toggle="tab" href="#THleads">
          <i class="fa fa-tty"></i> <?php echo _l('leads'); ?> (<span id="THcleads">0</span>)
        </a>
      </li>
      <li>
        <a class="search-tabs-a" data-toggle="tab" href="#THprojects">
          <i class="fa fa-handshake-o"></i> <?php echo _l('projects'); ?> (<span id="THcprojects">0</span>)
        </a>
      </li>
      <li>
        <a class="search-tabs-a" data-toggle="tab" href="#THclients">
          <i class="fa fa-building"></i> <?php echo _l('als_clients'); ?> (<span id="THcclients">0</span>)
        </a>
      </li>
      <li>
        <a class="search-tabs-a" data-toggle="tab" href="#THcontacts">
          <i class="fa fa-user"></i> <?php echo _l('Person'); ?> (<span id="THccontacts">0</span>)
        </a>
      </li>
    </ol>
</div>

<div class="tab-content">
  <div id="THall" class="tab-pane fade in active"></div>
  <div id="THleads" class="tab-pane fade"> </div>
  <div id="THprojects" class="tab-pane fade"> </div>
  <div id="THclients" class="tab-pane fade"> </div>
  <div id="THcontacts" class="tab-pane fade"> </div>
</div>