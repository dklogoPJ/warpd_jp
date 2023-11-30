<div class="menu">

    <?php echo $this->element('menu_user_profile'); ?>

    <ul class="navigation">
        <!-- Filter menus on bdc admin or user -->

        <li class="active">
            <a href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>">
                <span class="isw-grid"></span><span class="text">Dashboard</span>
            </a>
        </li>
        <!-- Filter menus on user type-->

        <li class="openable">
            <a href="#">
                <span class="isw-list"></span><span class="text">UI elements</span>
            </a>
            <ul>
                <li>
                    <a href="ui.html">
                        <span class="icon-th"></span><span class="text">UI Elements</span>
                    </a>
                </li>
                <li>
                    <a href="widgets.html">
                        <span class="icon-th-large"></span><span class="text">Widgets</span>
                    </a>
                </li>
                <li>
                    <a href="buttons.html">
                        <span class="icon-chevron-right"></span><span class="text">Buttons</span>
                    </a>
                </li>
                <li>
                    <a href="icons.html">
                        <span class="icon-fire"></span><span class="text">Icons</span>
                    </a>
                </li>
                <li>
                    <a href="grid.html">
                        <span class="icon-align-justify"></span><span class="text">Grid system</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="forms.html">
                <span class="isw-archive"></span><span class="text">Forms stuff</span>
            </a>
        </li>
        <li class="openable">
            <a href="#">
                <span class="isw-chat"></span><span class="text">Messages</span>
            </a>
            <ul>
                <li>
                    <a href="messages.html">
                        <span class="icon-comment"></span><span class="text">Messages widgets</span></a>

                    <a href="#" class="caption yellow link_navPopMessages">5</a>

                    <div id="navPopMessages" class="popup" style="display: none;">
                        <div class="head clearfix">
                            <div class="arrow"></div>
                            <span class="isw-chats"></span>
                            <span class="name">Personal messages</span>
                        </div>
                        <div class="body messages">

                            <div class="item clearfix">
                                <div class="image"><a href="#"><img src="img/users/aqvatarius.jpg" class="img-polaroid"/></a></div>
                                <div class="info">
                                    <a href="#" class="name">Aqvatarius</a>
                                    <p>Lorem ipsum dolor. In id adipiscing diam. Sed lobortis dui ut odio tempor blandit. Suspendisse scelerisque mi nec nunc gravida quis mollis lacus dignissim.</p>
                                    <span>19 feb 2012 12:45</span>
                                </div>
                            </div>

                            <div class="item clearfix">
                                <div class="image"><a href="#"><img src="img/users/olga.jpg" class="img-polaroid"/></a></div>
                                <div class="info">
                                    <a href="#" class="name">Olga</a>
                                    <p>Cras nec risus dolor, ut tristique neque. Donec mauris sapien, pellentesque at porta id, varius eu tellus.</p>
                                    <span>18 feb 2012 12:45</span>
                                </div>
                            </div>

                            <div class="item clearfix">
                                <div class="image"><a href="#"><img src="img/users/dmitry.jpg" class="img-polaroid"/></a></div>
                                <div class="info">
                                    <a href="#" class="name">Dmitry</a>
                                    <p>In id adipiscing diam. Sed lobortis dui ut odio tempor blandit.</p>
                                    <span>17 feb 2012 12:45</span>
                                </div>
                            </div>

                            <div class="item clearfix">
                                <div class="image"><a href="#"><img src="img/users/helen.jpg" class="img-polaroid"/></a></div>
                                <div class="info">
                                    <a href="#" class="name">Helen</a>
                                    <p>Sed lobortis dui ut odio tempor blandit. Suspendisse scelerisque mi nec nunc gravida quis mollis lacus dignissim. Donec mauris sapien, pellentesque at porta id, varius eu tellus.</p>
                                    <span>15 feb 2012 12:45</span>
                                </div>
                            </div>

                        </div>
                        <div class="footer">
                            <button class="btn link_navPopMessages" type="button">Close</button>
                        </div>
                    </div>
                </li>
            </ul>


        </li>
        <li>
            <a href="statistic.html">
                <span class="isw-graph"></span><span class="text">Statistics</span>
            </a>
        </li>
        <li>
            <a href="tables.html">
                <span class="isw-text_document"></span><span class="text">Tables</span>
            </a>
        </li>
        <li class="openable">
            <a href="#">
                <span class="isw-documents"></span><span class="text">Sample pages</span>
            </a>
            <ul>
                <li>
                    <a href="user.html">
                        <span class="icon-info-sign"></span><span class="text">User info</span>
                    </a>
                </li>
                <li>
                    <a href="profile.html">
                        <span class="icon-user"></span><span class="text">User profile</span>
                    </a>
                </li>
                <li>
                    <a href="users.html">
                        <span class="icon-list"></span><span class="text">Users</span>
                    </a>
                </li>
                <li>
                    <a href="stream.html">
                        <span class="icon-refresh"></span><span class="text">Stream activity</span>
                    </a>
                </li>
                <li>
                    <a href="mail.html">
                        <span class="icon-envelope"></span><span class="text">Mailbox</span>
                    </a>
                </li>
                <li>
                    <a href="edit.html">
                        <span class="icon-pencil"></span><span class="text">User edit</span>
                    </a>
                </li>
                <li>
                    <a href="faq.html">
                        <span class="icon-question-sign"></span><span class="text">FAQ</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="openable">
            <a href="#">
                <span class="isw-zoom"></span><span class="text">Other</span>
            </a>
            <ul>
                <li>
                    <a href="gallery.html">
                        <span class="icon-picture"></span><span class="text">Gallery</span>
                    </a>
                </li>
                <li>
                    <a href="typography.html">
                        <span class="icon-pencil"></span><span class="text">Typography</span>
                    </a>
                </li>
                <li>
                    <a href="files.html">
                        <span class="icon-upload"></span><span class="text">File handling</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="openable">
            <a href="#">
                <span class="isw-cancel"></span><span class="text">Error pages</span>
            </a>
            <ul>
                <li><a href="403.html"><span class="icon-warning-sign"></span><span class="text">403 Forbidden</span></a></li>
                <li><a href="404.html"><span class="icon-warning-sign"></span><span class="text">404 Not Found</span></a></li>
                <li><a href="500.html"><span class="icon-warning-sign"></span><span class="text">500 Internal Server Error</span></a></li>
                <li><a href="503.html"><span class="icon-warning-sign"></span><span class="text">503 Service Unavailable</span></a></li>
                <li><a href="504.html"><span class="icon-warning-sign"></span><span class="text">504 Gateway Timeout</span></a></li>
            </ul>
        </li>
    </ul>

    <div class="dr"><span></span></div>

    <div class="widget-fluid">
        <div id="menuDatepicker"></div>
    </div>

    <div class="dr"><span></span></div>

    <div class="widget">

        <div class="input-append">
            <input id="appendedInputButton" style="width: 118px;" type="text"><button class="btn" type="button">Search</button>
        </div>

    </div>

    <div class="dr"><span></span></div>

    <div class="widget-fluid">

        <div class="wBlock clearfix">
            <div class="dSpace">
                <h3>Last visits</h3>
                <span class="number">6,302</span>
                <span>5,774 <b>unique</b></span>
                <span>3,512 <b>returning</b></span>
            </div>
            <div class="rSpace">
                <h3>Today</h3>
                <span class="mChartBar" sparkType="bar" sparkBarColor="white"><!--240,234,150,290,310,240,210,400,320,198,250,222,111,240,221,340,250,190--></span>
                <span>&nbsp;</span>
                <span>65% <b>New</b></span>
                <span>35% <b>Returning</b></span>
            </div>
        </div>

    </div>

</div>
