<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">

        <!--=== Navigation ===-->

        <ul id="nav">

            <li class="<?php if($this->uri->segment(2)=='curriculum'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/curriculum'); ?>">
                    <i class="fa fa-university"></i>
                    Curriculum
                </a>
            </li>

            <li class="<?php if($this->uri->segment(2)=='courses'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/courses'); ?>">
                    <i class="fa fa-th-large"></i>
                    Courses
                </a>
            </li>


            <li class="<?php if($this->uri->segment(2)=='academic-year'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/academic-year'); ?>">
                    <i class="fa fa-calendar-check-o"></i>
                    Academic Year
                </a>
            </li>


            <li class="<?php if($this->uri->segment(2)=='semester-antara'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/semester-antara'); ?>">
                    <i class="fa fa-random"></i>
                    Semester Antara
                </a>
            </li>

<!--            <li class="">-->
<!--                <a href="#">-->
<!--                    <i class="fa fa-percent"></i>-->
<!--                    Beasiswa-->
<!--                </a>-->
<!--            </li>-->

<!--            <li class="--><?php //if($this->uri->segment(2)=='ketersediaan-dosen'){echo "current";} ?><!--">-->
<!--                <a href="--><?php //echo base_url('academic/ketersediaan-dosen'); ?><!--">-->
<!--                    <i class="fa fa-pencil-square-o"></i>-->
<!--                    Ketersediaan Dosen-->
<!--                </a>-->
<!--            </li>-->
            <li class="<?php if($this->uri->segment(2)=='reference'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/reference'); ?>">
                    <i class="fa fa-external-link-square"></i>
                    Reference
                </a>
            </li>

        </ul>
        <div class="sidebar-title">
            <span>Academic Transactions</span>
        </div>
        <ul id="nav">
            <li class="<?php if($this->uri->segment(2)=='timetables'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/timetables'); ?>">
                    <i class="fa fa-calendar"></i>
                    Timetables
                </a>
            </li>
<!--            <li class="">-->
<!--                <a href="#">-->
<!--                    <i class="fa fa-refresh"></i>-->
<!--                    Kelas Pengganti-->
<!--                </a>-->
<!--            </li>-->
            <li class="<?php if($this->uri->segment(2)=='study-planning'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/study-planning'); ?>">
                    <i class="fa fa-tasks"></i>
                    Study Plan
                </a>
            </li>
            <li class="<?php if($this->uri->segment(2)=='attendance'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/attendance'); ?>">
                    <i class="fa fa-users"></i>
                    Attendance
                </a>
            </li>
            <li class="<?php if($this->uri->segment(2)=='exam-schedule'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/exam-schedule'); ?>">
                    <i class="fa fa-sitemap"></i>
                    Exam Schedule
                </a>
            </li>
            <li class="<?php if($this->uri->segment(2)=='score'){echo "current";} ?>">
                <a href="<?php echo base_url('academic/score') ?>">
                    <i class="fa fa-area-chart"></i>
                    Score
                </a>
            </li>
            <li class="">
                <a href="#">
                    <i class="fa fa-flag"></i>
                    Final Project (Coming Soon)
                </a>
            </li>
        </ul>


        <div class="sidebar-widget align-center">
            <div class="btn-group" data-toggle="buttons" id="theme-switcher">
                <label class="btn active">
                    <input type="radio" name="theme-switcher" data-theme="bright"><i class="fa fa-sun-o"></i> Bright
                </label>
                <label class="btn">
                    <input type="radio" name="theme-switcher" data-theme="dark"><i class="fa fa-moon-o"></i> Dark
                </label>
            </div>
        </div>

    </div>
    <div id="divider" class="resizeable"></div>
</div>
<!-- /Sidebar -->

