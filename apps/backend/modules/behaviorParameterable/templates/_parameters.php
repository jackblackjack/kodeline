<div class="mws-panel grid_8 mws-collapsible">
  <div class="mws-panel-header">
    <span><i class="icon-table"></i> <?php echo $object['title'] ?> / Поля типа</span>
    <div class="mws-collapse-button mws-inset"><span></span></div>
  </div>

  <div class="mws-panel-toolbar">
      <div class="btn-toolbar">
          <div class="btn-group">
            <?php if ($objectParamValue): ?>
            <a href="<?php echo url_for('@parameterable_component_parameter_new?model=' . $modelName . '&belong=' . $objectParamValue) ?>" class="btn">
            <?php else: ?>
            <a href="<?php echo url_for('@parameterable_component_parameter_new?model=' . $modelName) ?>" class="btn">
            <?php endif ?>
            <i class="icol-add"></i> Новое поле</a>
            <a href="#" class="btn"><i class="icol-cross"></i> Удалить выбранные</a>
          </div>
      </div>
  </div>

  <?php if ($parametersSchema->count()): ?>
  <div class="mws-panel-body no-padding">
    <div id="DataTables_Table_1_wrapper" class="dataTables_wrapper" role="grid">
      <div id="DataTables_Table_1_length" class="dataTables_length">
        <label>Показывать по 
          <select size="1" name="DataTables_Table_1_length" aria-controls="DataTables_Table_1">
            <option value="10" selected="selected">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
          </select> полей
        </label>
      </div>

      <div class="dataTables_filter" id="DataTables_Table_1_filter">
        <label>Поиск поля: <input type="text" aria-controls="DataTables_Table_1"></label>
      </div>

      <table class="mws-datatable-fn mws-table dataTable" aria-describedby="DataTables_Table_1_info">
        <thead>
          <tr role="row">
            <th class="checkbox-column" tabindex="0"><input type="checkbox" name="selected" value="all"></th>
            <th tabindex="0">
              Метка поля
              
              <a href="#" class="btn dropdown-toggle" data-toggle="dropdown" tabindex="0">RU <span class="caret"></span></a>
              <ul class="dropdown-menu pull-right">
                  <li><a href="#"><i class="icol-disconnect"></i> Disconnect From Server</a></li>
                  <li class="divider"></li>
                  <li class="dropdown-submenu">
                      <a href="#">More Options</a>
                      <ul class="dropdown-menu">
                          <li><a href="#">Contact Administrator</a></li>
                          <li><a href="#">Destroy Table</a></li>
                      </ul>
                  </li>
              </ul>
            </th>
            <th class="sorting" role="columnheader" tabindex="0">Имя</th>
            <th class="sorting" role="columnheader" tabindex="0">Тип</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($parametersSchema as $schema): ?>
            <?php include_partial($sf_context->getModuleName() . '/parametersDefinitionList', array('modelName' => $modelName, 'schema' => $schema)) ?>
          <?php endforeach ?>
        </tbody>
      </table>

      <!-- paging -->
      <div class="dataTables_info" id="DataTables_Table_1_info">Showing 1 to 10 of 57 entries</div>
      <div class="dataTables_paginate paging_full_numbers" id="DataTables_Table_1_paginate">
        <a tabindex="0" class="first paginate_button paginate_button_disabled" id="DataTables_Table_1_first">First</a>
        <a tabindex="0" class="previous paginate_button paginate_button_disabled" id="DataTables_Table_1_previous">Previous</a>
        <span>
          <a tabindex="0" class="paginate_active">1</a>
          <a tabindex="0" class="paginate_button">2</a>
          <a tabindex="0" class="paginate_button">3</a>
          <a tabindex="0" class="paginate_button">4</a>
          <a tabindex="0" class="paginate_button">5</a>
        </span>

        <a tabindex="0" class="next paginate_button" id="DataTables_Table_1_next">Next</a>
        <a tabindex="0" class="last paginate_button" id="DataTables_Table_1_last">Last</a>
      </div>
      <!-- // paging -->
    </div>
  </div>
  <?php endif ?>
</div>
