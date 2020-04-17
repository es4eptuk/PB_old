<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
    
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">МЕНЮ</li>
        
       <?php 
       if ($userdata['group'] == 1 || $userdata['group'] == 2) {
       echo '
       
       <li class="treeview">
        <a href="#">
            <i class="fa fa-dropbox"></i> <span>Склад</span>
            <span class="pull-right-container">
              
            </span>
          </a>
       <ul class="treeview-menu">
       
       <li class="treeview">
           <a href="#">
               <i class="fa fa-th"></i> <span>Номенклатура</span>
               <span class="pull-right-container">

            </span>
           </a>
           <ul class="treeview-menu">
               <li><a href="./add_pos.php"><i class="fa fa-circle-o"></i>Добавить позицию</a></li>
               <li><a href="./pos.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
               <li><a href="./pos.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
               <li><a href="./pos.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
               <li><a href="./pos.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
               <li><a href="./pos.php?id=7"><i class="fa fa-circle-o"></i> Листовые материалы</a></li>
           </ul>

       </li>

           <li class="treeview">
               <a href="#">
                   <i class="fa fa-th-large "></i> <span>Складские остатки</span>
                   <span class="pull-right-container">

            </span>
               </a>
               <ul class="treeview-menu">
                   <li><a href="./pos_on_robot.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
                   <li><a href="./pos_on_robot.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
                   <li><a href="./pos_on_robot.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
                   <li><a href="./pos_on_robot.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
                   <li><a href="./pos_on_robot.php?id=7"><i class="fa fa-circle-o"></i> Листовые материалы</a></li>
               </ul>

           </li>
        
        
        
         <li class="treeview">
          <a href="#">
            <i class="fa fa-list"></i> <span>Заказы</span>
            <span class="pull-right-container">
              
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="./add_order.php"><i class="fa fa-circle-o"></i>Добавить заказ</a></li>
            <li><a href="./orders.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
            <li><a href="./orders.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
            <li><a href="./orders.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
            <li><a href="./orders.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
            <li><a href="./orders.php?id=7"><i class="fa fa-circle-o"></i> Листовые материалы</a></li>
            <li><a href="./orders.php?id=999"><i class="fa fa-circle-o"></i> Возвраты поставщику</a></li>
            <li><a href="./orders.php?id=998"><i class="fa fa-circle-o"></i> Покраска/Покрытие</a></li>
            <li><a href="./orders.php?id=997"><i class="fa fa-circle-o"></i> Сварка/Зенковка</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-plus-square"></i> <span>Поступления</span>
            <span class="pull-right-container">
              
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="./add_admission.php"><i class="fa fa-circle-o"></i>Добавить поступление</a></li>
            <li><a href="./admissions.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
            <li><a href="./admissions.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
            <li><a href="./admissions.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
            <li><a href="./admissions.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
              <li><a href="./admissions.php?id=7"><i class="fa fa-circle-o"></i> Листовые материалы</a></li>
          </ul>
          
        </li>
        
         <li >
          <a href="./writeoff.php">
            <i class="fa fa-minus-square"></i> <span>Списания</span>
           
          </a>
          
          
        </li>

        <li >
          <a href="./assembly.php">
            <i class="fa fa-codepen"></i> <span>Сборки</span>
          </a>
          
          
        </li>
        
        <li >
          <a href="./kit.php">
            <i class="fa fa-cubes"></i> <span>Комплекты</span>
          </a>
          
          
        </li>
        
        
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-calendar"></i> <span>График заказов</span>
            <span class="pull-right-container">
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="./plan.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
            <li><a href="./plan.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
            <li><a href="./plan.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
            <li><a href="./plan.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
              <li><a href="./plan.php?id=7"><i class="fa fa-circle-o"></i> Листовые материалы</a></li>
          </ul>
        </li>
        
        <li>
          <a href="./paid_bills.php">
            <i class="fa fa-money"></i> <span>Оплаченные счета</span>
          </a>
       </li>
       <li class="treeview">
           <a href="#">
               <i class="fa fa-archive"></i> <span>Удаленный склад</span>
               <span class="pull-right-container">

            </span>
           </a>
           <ul class="treeview-menu">
               <li><a href="./pos_warehouse.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
               <li><a href="./pos_warehouse.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
               <li><a href="./pos_warehouse.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
               <li><a href="./pos_warehouse.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
               <li><a href="./pos_warehouse.php?id=7"><i class="fa fa-circle-o"></i> Листовые материалы</a></li>
           </ul>

       </li>        
       </ul>
       </li>
       ';
       
       
       }
       
       
        
        if ($userdata['user_id'] == 14 || $userdata['user_id'] == 75  || $userdata['user_id'] == 15 || $userdata['user_id'] == 37) {
       echo '    
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-money"></i> <span>Финансы</span>
            <span class="pull-right-container">
              
            </span>
          </a>
          
          <ul class="treeview-menu">
            <li><a href="./cost_price.php"><i class="fa fa-circle-o"></i>Себестоиимость</a></li>
            <li><a href="./cost_items.php"><i class="fa fa-circle-o"></i>Расходы</a></li>
            <li><a href="./cost_income.php"><i class="fa fa-circle-o"></i>Доходы</a></li>
            <li><a href="./cost_statistics.php"><i class="fa fa-circle-o"></i>Статистика расходов</a></li>
            <li><a href="./cost_profit.php"><i class="fa fa-circle-o"></i>Прибыль</a></li>
            <li class="treeview"><a href=""><i class="fa fa-circle-o"></i>Справочники</a>
                    <ul class="treeview-menu">
                         <li><a href="./cost_category.php"><i class="fa fa-circle-o"></i>Расходы</a></li>
                         <li><a href="./cost_income_category.php"><i class="fa fa-circle-o"></i>Доходы</a></li>
                    </ul>
            </li>
          </ul>
         
          
        </li>
        
        ';
        }
        
        if ($userdata['group'] == 1 || $userdata['group'] == 3 ) {
       echo '
        
        <li>
          <a href="./robots.php">
            <i class="fa fa-android"></i> <span>Роботы</span>
            <span class="pull-right-container">
              
            </span>
          </a>
         
          
        </li>
 
 ';
 }
 
 if ($userdata['group'] == 1  ) {
       echo '

        
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-check-square"></i> <span>Справочники</span>
            <span class="pull-right-container">
            </span>
          </a>
          <ul class="treeview-menu">
          
            <li class="treeview">
              <a href="#">
                <i class="fa fa-dropbox"></i><span> Склад</span>
                <span class="pull-right-container">
                </span>
              </a>
              <ul class="treeview-menu">
                <li ><a href="./contragents.php"><i class="fa fa-compress"></i><span> Контрагенты</span></a></li>
              </ul>
            </li> 
                      
            <li class="treeview">
              <a href="#">
                <i class="fa fa-android"></i><span> Роботы</span>
                <span class="pull-right-container">
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="./equipment.php"><i class="fa fa-th-list"></i><span> Комплектации</span></a></li>
                <li class="treeview">
                    <a href="#"><i class="fa fa-list"></i> Чек - листы (Комплектации)</a>
                    <ul class="treeview-menu">
                    <li><a href="./checks.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
                    <li><a href="./checks.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
                    <li><a href="./checks.php?id=5"><i class="fa fa-circle-o"></i> Настройка</a></li>
                    <li><a href="./checks.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
                    <li><a href="./checks.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
                    </ul>
                </li>
                <li><a href="./options.php"><i class="fa fa-plus-square"></i> Опции</a></li>                
                <li class="treeview">
                    <a href="#"><i class="fa fa-list"></i> Чек - листы (Опции)</a>
                    <ul class="treeview-menu">
                    <li><a href="./checks_options.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
                    <li><a href="./checks_options.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
                    <li><a href="./checks_options.php?id=5"><i class="fa fa-circle-o"></i> Настройка</a></li>
                    <li><a href="./checks_options.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
                    <li><a href="./checks_options.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
                    </ul>
                </li>                
              </ul>
            </li>          

            <li class="treeview">
              <a href="#">
                <i class="fa fa-life-ring"></i><span> Тех. поддержка</span>
                <span class="pull-right-container">
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="./ticket_category.php"><i class="fa fa-th-list"></i> Категории обращений (Тикет)</a></li>
              </ul>
            </li> 


          </ul>
          
        </li>
        
        ';
        }
        
    if ($userdata['group'] == 1 || $userdata['group'] == 4) {
       echo ' 
       
       <li class="treeview">
          <a href="#">
            <i class="fa fa-life-ring"></i> <span>Тех. поддержка</span>
            <span class="pull-right-container">
              
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li><a href="./cards_robot.php"><i class="fa fa-address-card"></i> Роботы</a></li>
            <li><a href="./kanban.php"><i class="fa fa-tasks"></i> KanBan</a></li>
            <li><a href="./archive.php"><i class="fa fa-archive"></i> Архив</a></li>
            <li><a href="./ts_stats.php"><i class="fa fa-archive"></i> Статистика</a></li>
              <li class="treeview">
          <a href="#">
            <i class="fa fa-th"></i> <span>Каталог запчастей</span>
            <span class="pull-right-container">
              
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="./catalog.php?id=1"><i class="fa fa-circle-o"></i> Механика</a></li>
            <li><a href="./catalog.php?id=2"><i class="fa fa-circle-o"></i> Аппаратка</a></li>
            <li><a href="./catalog.php?id=3"><i class="fa fa-circle-o"></i> Корпус</a></li>
            <li><a href="./catalog.php?id=4"><i class="fa fa-circle-o"></i> Упаковка</a></li>
          </ul>
          
        </li>

          </ul>
          
        </li>

       
       ';
       }
       
       if ($userdata['group'] == 1 || $userdata['group'] == 5) {
       echo ' 
       
       <li class="treeview">
          <a href="#">
            <i class="fa fa-clock-o"></i> <span>Планировщик</span>
            <span class="pull-right-container">
              
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li><a href="./tasks_add.php"><i class="fa fa-address-card"></i> Добавить событие</a></li>
            <li><a href="./tasks.php"><i class="fa fa-tasks"></i> Задачи</a></li>
           
            
          </ul>
          
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span>Администрирование</span>
            <span class="pull-right-container">
              
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li><a href="./users.php"><i class="fa fa-user"></i>Пользователи</a></li>
            <li><a href="./users_group.php"><i class="fa fa-users"></i>Группы</a></li>
           
            
          </ul>
          
        </li>

       
       ';
       }
        
        
        ?>
      </ul>
    </section>
    <!-- /.sidebar -->


  </aside>