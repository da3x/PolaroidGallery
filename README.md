PolaroidGallery
===============

A simple polaroid style gallery using PHP, HTML, CSS and some JavaScript which integrates Google Maps and HTML5 video.

See [http://gallery.da3x.de/show/example](http://gallery.da3x.de/show/example) for a quick preview.

## Introduction

A few months ago – maybe a few years already – I've felt the need to host private galleries to share photos with my family without exposing them to the public. So I spent some time to create a visual appealing gallery using PHP to dynamically present images from simple folders uploaded to my webspace.

As time went by other services improved on sharing private galleries and for some time now I prefer DropBox shared links which I can initiate directly from my iPhone – which is quite effective and easy.

So I've decided to make this gallery public for all you interested in picking up development. 

## Features

Let me name some of the features... this list will be improved later.

* supports german and english
* simply create a new folder as `/galleries/name` and point your browser to `http://gallery.domain.com/show/name` to open the gallery
* scans for all images and videos automatically
* reads EXIF information for date and location if available
* generates thumbnails and previews (automatic rotation)
* creates a zip archive to download all images
* supports simple comments using json files for storage and cookies to remember visitors
* uses [Pushover](http://www.pushover.net) to notify you of comments and visitors
* ...

## Requirements

The run this gallery you'll need to fulfill some requirements. I won't spend more time to make it easier or more flexible. Feel free to fork your own version and change the things you need to change.

* webspace with PHP support (with some extension for EXIF and others)
* the gallery needs to be placed at the root of that webspace
* mod_rewrite is used for pretty URLs (see .htaccess)
* optional [Pushover](http://www.pushover.net) account for notifications
* ...

## Future

Right now I don't plan to invest any more time into this project. That's why I've made it public. Feel free to fork your own version and make all changes you feel need for. I'll keep an eye on incoming pull requests and pull things in I find useful. Thanks!