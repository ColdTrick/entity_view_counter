# Entity view counter

[![Build Status](https://scrutinizer-ci.com/g/ColdTrick/entity_view_counter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ColdTrick/entity_view_counter/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ColdTrick/entity_view_counter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ColdTrick/entity_view_counter/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/coldtrick/entity_view_counter/v/stable.svg)](https://packagist.org/packages/coldtrick/entity_view_counter)
[![License](https://poser.pugx.org/coldtrick/entity_view_counter/license.svg)](https://packagist.org/packages/coldtrick/entity_view_counter)

Count the views of the configured registered entity types. The views will only be counted on the full view of an entity
and not for the owner of the entity. Furthermore only one view will be counted per user session.

## Features

- track the full view of the selected entity types
- spider and bot (search engine) views will in most cases not be tracked, so this should give a fair count

## Installation instructions

Once this plugin is enabled it will NOT track anything by default.

Go to the plugin settings (http://your-site-url/admin/plugin_settings/entity_view_counter) and select the entity types you wish to track.

If you enabled or disabled a plugin, please revisit the plugin settings page. Their may be more/less entity types available, check if the configuration is still what your want.
