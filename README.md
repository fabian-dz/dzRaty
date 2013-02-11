dzRaty
======

DzRaty is an extension for Yii framework. It is a wrapper for [jQuery Raty](http://wbotelhos.com/raty), a plugin developed by [Washington Botelho](http://wbotelhos.com/) that generates a customizable star rating.

To get started, check [http://yii.dezero.es/rating](http://yii.dezero.es/rating)

## Installation
Requirements: jQuery and Yii framework 1.0 or above (tested on 1.1.12).

Extract downloaded file to your Yii application extensions folder (default: protected/extensions).

## Usage

### Edit mode
Using with an attribute model. *weight* is a sample attribute name
<pre>
$this->widget('ext.DzRaty.DzRaty', array(
	'model' => $model,
	'attribute' => 'weight',
));
</pre>

Using with a single input element
<pre>
$this->widget('ext.DzRaty.DzRaty', array(
	'name' => 'my_rating_field',
	'value' => 3,
));
</pre>

### View / Read-only mode
<pre>
$this->widget('ext.DzRaty.DzRaty', array(
	'name' => 'my_rating_field',
	'value' => 3,
	'options' => array(
		'readOnly' => TRUE,
	),
));
</pre>

## Localization - i18n
DzRaty translates all translatable elements of jQuery Star plugin. You can place your own translation file under DzRaty/messages.

Current version contains translation files for spanish. You could simply duplicate and edit one of them.

## Resources
- [DzRaty Homepage](http://yii.dezero.es/dzraty)
- [DzRaty Github Page](https://github.com/fabian-dz/dzRaty)
- [jQuery Raty Homepage](http://wbotelhos.com/raty)
- [jQuery Raty Github Page](https://github.com/wbotelhos/raty)


> [www.dezero.es](http://www.dezero.es)

