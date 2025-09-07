<div id="token" token="<?= $_SESSION["token"] ?>"></div>

<header class="app-header-vertical">
<!-- Mobile Hamburger Toggle (No JS) -->
<input type="checkbox" id="menu-toggle" class="menu-toggle" hidden>
<label for="menu-toggle" class="hamburger" aria-label="Toggle menu">
    <i class="fas fa-bars"></i>
</label>


    <div class="sidebar" id="sidebar">

        <!-- Logo -->
        <div class="sidebar-logo">
            <a href="/" title="<?= htmlentities($_SESSION["APP_NAME"]) ?>">
                <img src="/images/logo-header.svg" alt="<?= htmlentities($_SESSION["APP_NAME"]) ?>" class="sidebar-logo-img">
                <span class="sidebar-label"><?= htmlentities($_SESSION["APP_NAME"]) ?></span>
            </a>
        </div>

        <!-- Usage -->
        <div class="sidebar-usage">
            <?php if ($_SESSION["look"] !== "") {
                $user_icon = "fa-binoculars";
            } elseif ($_SESSION["userContext"] === "admin") {
                $user_icon = "fa-user-tie";
            } else {
                $user_icon = "fa-user";
            } ?>
            <div class="sidebar-usage-inner">
                <div class="sidebar-usage-item">
                    <i class="fas <?= $user_icon ?>"></i>
                    <span class="sidebar-label u-text-bold"><?= htmlspecialchars($user) ?></span>
                </div>
                <div class="sidebar-usage-item">
                    <i class="fas fa-hard-drive"></i>
                    <span class="sidebar-label"><?= humanize_usage_size($panel[$user]["U_DISK"]) ?> / <?= humanize_usage_size($panel[$user]["DISK_QUOTA"]) ?></span>
                </div>
                <div class="sidebar-usage-item">
                    <i class="fas fa-right-left"></i>
                    <span class="sidebar-label"><?= humanize_usage_size($panel[$user]["U_BANDWIDTH"]) ?> / <?= humanize_usage_size($panel[$user]["BANDWIDTH"]) ?></span>
                </div>
            </div>
        </div>
<!---------- Start ------------------->		
			<!-- Notifications / Menu wrapper -->
			<div>

				<!-- Notifications -->
				<?php
    $impersonatingAdmin = $_SESSION["userContext"] === "admin" && ($_SESSION["look"] !== "" && $user == "admin");
    // Do not show notifications panel when impersonating 'admin' user
    if (!$impersonatingAdmin) { ?>
					<div x-data="notifications" class="top-bar-notifications">
						<button
							x-on:click="toggle()"
							x-bind:class="open && 'active'"
							class="top-bar-menu-link"
							type="button"
							title="<?= _("Notifications") ?>"
						>
							<i
								x-bind:class="{
									'animate__animated animate__swing icon-orange': (!initialized && <?= $panel[$user]["NOTIFICATIONS"] == "yes" ? "true" : "false" ?>) || notifications.length != 0,
									'fas fa-bell': true
								}"
							></i>
							<span class="u-hidden"><?= _("Notifications") ?></span>
						</button>
						<div
							x-cloak
							x-show="open"
							x-on:click.outside="open = false"
							class="top-bar-notifications-panel"
						>
							<template x-if="!initialized">
								<div class="top-bar-notifications-empty">
									<i class="fas fa-circle-notch fa-spin icon-dim"></i>
									<p><?= _("Loading...") ?></p>
								</div>
							</template>
							<template x-if="initialized && notifications.length == 0">
								<div class="top-bar-notifications-empty">
									<i class="fas fa-bell-slash icon-dim"></i>
									<p><?= _("No notifications") ?></p>
								</div>
							</template>
							<template x-if="initialized && notifications.length > 0">
								<ul>
									<template x-for="notification in notifications" :key="notification.ID">
										<li
											x-bind:id="`notification-${notification.ID}`"
											x-bind:class="notification.ACK && 'unseen'"
											class="top-bar-notification-item"
											x-data="{ open: true }"
											x-show="open"
											x-collapse
										>
											<div class="top-bar-notification-inner">
												<div class="top-bar-notification-header">
													<p x-text="notification.TOPIC" class="top-bar-notification-title"></p>
													<button
														x-on:click="open = false; setTimeout(() => remove(notification.ID), 300);"
														type="button"
														class="top-bar-notification-delete"
														title="<?= _("Delete notification") ?>"
													>
														<i class="fas fa-xmark"></i>
														<span class="u-hidden-visually"><?= _("Delete notification") ?></span>
													</button>
												</div>
												<div class="top-bar-notification-content" x-html="notification.NOTICE"></div>
												<p class="top-bar-notification-timestamp">
													<time
														:datetime="`${notification.TIMESTAMP_ISO}`"
														x-bind:title="`${notification.TIMESTAMP_TITLE}`"
														x-text="`${notification.TIMESTAMP_TEXT}`"
													></time>
												</p>
											</div>
										</li>
									</template>
								</ul>
							</template>
							<template x-if="initialized && notifications.length > 2">
								<button
									x-on:click="removeAll()"
									type="button"
									class="top-bar-notifications-delete-all"
								>
									<i class="fas fa-check"></i>
									<?= _("Delete all notifications") ?>
								</button>
							</template>
						</div>
					</div>
				<?php } ?>
			</div>
		
<!--------- END ---------------->		
        <!-- Tabs -->
        <nav class="sidebar-tabs" aria-label="Sidebar">
    <ul>
        <li>
            <a href="/list/dashboard/" class="<?php if (in_array($TAB, ["DASHBOARD"])) echo "active"; ?>">
                <i class="fas fa-gauge"></i>
                <span class="sidebar-label"><?= _("Dashboard") ?></span>
            </a>
        </li>

        <?php if (
            isset($_SESSION["user"]) && $_SESSION["user"] === "admin" &&
            isset($_SESSION["userContext"]) && strtolower($_SESSION["userContext"]) === "admin" &&
            empty($_SESSION["look"])
        ): ?>
            <li>
                <a href="/list/user/"
                   class="<?php if (in_array($TAB, ["USER", "LOG"])) echo "active"; ?>"
                   data-toggle="submenu">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-label"><?= _("Users") ?></span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="/add/user/"><i class="fas fa-user-plus"></i><span class="sidebar-label"><?= _("Add User") ?></span></a></li>
                    <li><a href="/list/package/"><i class="fas fa-box"></i><span class="sidebar-label"><?= _("Add Package") ?></span></a></li>
                </ul>
            </li>
        <?php endif; ?>

        <li>
            <a href="/list/web/" class="<?php if (in_array($TAB, ["WEB"])) echo "active"; ?>" data-toggle="submenu">
                <i class="fas fa-globe-americas"></i>
                <span class="sidebar-label"><?= _("Web") ?></span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="/add/web/"><i class="fas fa-user-plus"></i><span class="sidebar-label"><?= _("Add Web") ?></span></a></li>
            </ul>
        </li>

        <li>
            <a href="/list/dns/" class="<?php if (in_array($TAB, ["DNS"])) echo "active"; ?>" data-toggle="submenu">
                <i class="fas fa-atlas"></i>
                <span class="sidebar-label"><?= _("DNS") ?></span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="/add/dns/"><i class="fas fa-user-plus"></i><span class="sidebar-label"><?= _("Add DNS") ?></span></a></li>
            </ul>
        </li>

        <li>
            <a href="/list/mail/" class="<?php if (in_array($TAB, ["MAIL"])) echo "active"; ?>" data-toggle="submenu">
                <i class="fas fa-envelope"></i>
                <span class="sidebar-label"><?= _("Mail") ?></span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="/add/mail/"><i class="fas fa-user-plus"></i><span class="sidebar-label"><?= _("Add Mail") ?></span></a></li>
            </ul>
        </li>

        <li>
            <a href="/list/db/" class="<?php if (in_array($TAB, ["DB"])) echo "active"; ?>" data-toggle="submenu">
                <i class="fas fa-database"></i>
                <span class="sidebar-label"><?= _("Databases") ?></span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="/add/db/"><i class="fas fa-user-plus"></i><span class="sidebar-label"><?= _("Add Database") ?></span></a></li>
            </ul>
        </li>

        <li>
            <a href="/list/cron/" class="<?php if (in_array($TAB, ["CRON"])) echo "active"; ?>" data-toggle="submenu">
                <i class="fas fa-clock"></i>
                <span class="sidebar-label"><?= _("Cron Jobs") ?></span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="/add/cron/"><i class="fas fa-user-plus"></i><span class="sidebar-label"><?= _("Add Cron") ?></span></a></li>
            </ul>
        </li>

        <li>
            <a href="/list/backup/" class="<?php if (in_array($TAB, ["BACKUP"])) echo "active"; ?>" data-toggle="submenu">
                <i class="fas fa-file-archive"></i>
                <span class="sidebar-label"><?= _("Backups") ?></span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="/add/backup/"><i class="fas fa-user-plus"></i><span class="sidebar-label"><?= _("Add Backup") ?></span></a></li>
            </ul>
        </li>
    </ul>
</nav>

 <!-- Menu -->
<nav class="sidebar-menu" aria-label="Sidebar Menu">
    <ul>
        <!-- Logs -->
        <?php if ($_SESSION["userContext"] === "admin" && !empty($_SESSION["look"]) && $user === "admin") { ?>
            <li class="top-bar-menu-item">
                <a title="<?= _("Logs") ?>" class="<?php if (in_array($TAB, ["LOG"])) echo "active"; ?>" href="/list/log/">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span class="sidebar-label"><?= _("Logs") ?></span>
                </a>
            </li>
        <?php } else { ?>
            <?php if ($panel[$user]["SUSPENDED"] === "no") { ?>
                <li class="top-bar-menu-item">
                    <a title="<?= htmlspecialchars($user) ?> (<?= htmlspecialchars($panel[$user]["NAME"]) ?>)" 
                       href="/edit/user/?user=<?= urlencode($user) ?>&token=<?= urlencode($_SESSION["token"]) ?>">
                        <i class="fas fa-circle-user"></i>
                        <span class="sidebar-label"><?= _("Edit User") ?> (<?= htmlspecialchars($panel[$user]["NAME"]) ?>)</span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>

        <!-- File Manager -->
        <?php if (!empty($_SESSION["FILE_MANAGER"]) && $_SESSION["FILE_MANAGER"] === "true") { ?>
            <?php if (!($_SESSION["userContext"] === "admin" && $_SESSION["look"] === "admin" && $_SESSION["POLICY_SYSTEM_PROTECTED_ADMIN"] === "yes")) { ?>
                <li>
                    <a title="<?= _("File Manager") ?>" class="<?php if ($TAB === "FM") echo "active"; ?>" href="/fm/">
                        <i class="fas fa-folder-open"></i>
                        <span class="sidebar-label"><?= _("File Manager") ?></span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>

        <!-- Web Terminal -->
        <?php if (!empty($_SESSION["WEB_TERMINAL"]) && $_SESSION["WEB_TERMINAL"] === "true") { ?>
            <?php if (!($_SESSION["userContext"] === "admin" && $_SESSION["look"] === "admin" && $_SESSION["POLICY_SYSTEM_PROTECTED_ADMIN"] === "yes") && $_SESSION["login_shell"] !== "nologin") { ?>
                <li>
                    <a title="<?= _("Web Terminal") ?>" class="<?php if ($TAB === "TERMINAL") echo "active"; ?>" href="/list/terminal/">
                        <i class="fas fa-terminal"></i>
                        <span class="sidebar-label"><?= _("Web Terminal") ?></span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>

        <!-- Server Settings -->
        <?php if (($_SESSION["userContext"] === "admin" && $_SESSION["POLICY_SYSTEM_HIDE_SERVICES"] !== "yes") || $_SESSION["user"] === "admin") { ?>
            <?php if (empty($_SESSION["look"])) { ?>
                <li class="top-bar-menu-item">
                    <a title="<?= _("Server Settings") ?>" class="<?php if (in_array($TAB, ["SERVER", "IP", "RRD", "FIREWALL"])) echo "active"; ?>" href="/list/server/">
                        <i class="fas fa-gear"></i>
                        <span class="sidebar-label"><?= _("Server Settings") ?></span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>

        <!-- Statistics -->
        <li class="top-bar-menu-item">
            <a title="<?= _("Statistics") ?>" class="<?php if (in_array($TAB, ["STATS"])) echo "active"; ?>" href="/list/stats/">
                <i class="fas fa-chart-line"></i>
                <span class="sidebar-label"><?= _("Statistics") ?></span>
            </a>
        </li>

        <!-- Help -->
        <?php if ($_SESSION["HIDE_DOCS"] !== "yes") { ?>
            <li class="top-bar-menu-item">
                <a title="<?= _("Help") ?>" href="https://hestiacp.com/docs/" target="_blank" rel="noopener">
                    <i class="fas fa-circle-question"></i>
                    <span class="sidebar-label"><?= _("Help") ?></span>
                </a>
            </li>
        <?php } ?>

        <!-- Logout -->
        <?php if (!empty($_SESSION["look"])) { ?>
            <li class="top-bar-menu-item">
                <a title="<?= _("Log out") ?> (<?= htmlspecialchars($user) ?>)" href="/logout/?token=<?= urlencode($_SESSION["token"]) ?>">
                    <i class="fas fa-circle-up"></i>
                    <span class="sidebar-label"><?= _("Log out") ?> (<?= htmlspecialchars($user) ?>)</span>
                </a>
            </li>
        <?php } else { ?>
            <li class="top-bar-menu-item">
                <a title="<?= _("Log out") ?>" href="/logout/?token=<?= urlencode($_SESSION["token"]) ?>">
                    <i class="fas fa-right-from-bracket"></i>
                    <span class="sidebar-label"><?= _("Log out") ?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
</nav>

    </div>
</header>
