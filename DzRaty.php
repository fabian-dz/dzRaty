<?php
/**
 * DzRaty widget class file
 *
 * This extension is a wrapper for jQuery Raty plugin
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://wbotelhos.com/raty
 * @link http://dezero.es/
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package extensions.DzRaty
 */

/**
 * DzRaty widget
 * @see http://wbotelhos.com/raty
 */
class DzRaty extends CInputWidget
{
	/**
	 * jQuery Raty options
	 *
	 * @var array
	 */
	public $options = array();
	

	/**
	 * jQuery Raty data / hints
	 *
	 * @var array
	 */
	public $data = array();


	/**
	 * File path to assets
	 *
	 * @var string
	 */
	protected $assetsPath;


	/**
	 * URL to assets
	 *
	 * @var string
	 */
	protected $assetsUrl;
	

	/**
	 * HTML attributes for the widget
	 * 
	 * @var array
	 */
	public $htmlOptions = array();


	/**
	 * HTML attributes for the widget target
	 * 
	 * @var array
	 */
	public $targetHtmlOptions = array();
	
	
	/**
	 * jQuery Raty options by default
	 * 
	 * @var array
	 */	
    private $_defaultOptions = array();
	
	
	/**
	 * Initializes the widget
	 */
	public function init()
	{
		// Assets
		if ( $this->assetsPath === NULL )
		{
			$this->assetsPath = dirname(__FILE__) . DIRECTORY_SEPARATOR .'assets';
		}
		
		if ( $this->assetsUrl === NULL )
		{
			$this->assetsUrl = Yii::app()->assetManager->publish($this->assetsPath);
		}
		
		// HTML Options
		list($this->name, $this->id) = $this->resolveNameID();
		if ( !isset($this->htmlOptions['id']) )
		{
			$this->htmlOptions['id'] = $this->id .'-raty';
			$this->targetHtmlOptions['id'] = $this->id;
		}
		if ( !isset($this->targetHtmlOptions['id']) )
		{
			$this->targetHtmlOptions['id'] = $this->id .'-target';
		}
		
		// Default jQuery Raty Options
		$this->_defaultOptions = array(
			'path' => $this->assetsUrl .'/img',
			'targetKeep' => TRUE,
			'targetType' => 'number',
			
			// Localization (i18n) for messages
			'hints' => array(
				Yii::t('DzRaty.DzRaty','bad'),
				Yii::t('DzRaty.DzRaty','poor'),
				Yii::t('DzRaty.DzRaty','regular'),
				Yii::t('DzRaty.DzRaty','good'),
				Yii::t('DzRaty.DzRaty','gorgeous')
			),
			'noRatedMsg' => Yii::t('DzRaty.DzRaty','Not rated yet!'),
			'cancelHint' => Yii::t('DzRaty.DzRaty', 'Cancel this rating!'),
		);
	}
	
	/**
	 * Runs the widget
	 */
	public function run()
	{
		if ( $this->hasModel() )
		{
			// echo CHtml::activeDropDownList($this->model, $this->attribute, $this->data, $this->targetHtmlOptions);
			$output_textfield = CHtml::activeTextField($this->model, $this->attribute, $this->targetHtmlOptions);
			$this->options['score'] = CHtml::resolveValue($this->model, $this->attribute);
		}
		else
		{
			// echo CHtml::dropDownList($this->name, $this->value, $this->data, $this->targetHtmlOptions);
			$output_textfield = CHtml::textField($this->name, $this->value, $this->targetHtmlOptions);
			$this->options['score'] = $this->value;
		}
		
		if ( !isset($this->htmlOptions['class']) )
		{
			$this->htmlOptions['class'] = 'raty-icons';
		}
		else
		{
			$this->htmlOptions['class'] .= ' raty-icons';
		}
		$this->htmlOptions['data-target'] = $this->targetHtmlOptions['id'];
		$this->htmlOptions['data-score'] = '';
		if ( !empty($this->options['score']) )
		{
			$this->htmlOptions['data-score'] = $this->options['score'];
		}
		
		

		echo CHtml::openTag('div', $this->htmlOptions) . '</div>' . $output_textfield;

		$this->registerClientScript();
	}
	
	
	/**
	 * Register required CSS and script files
	 */
	protected function registerClientScript()
	{
		$cs = Yii::app()->getClientScript();

		// Build jQuery Raty options
		// $options = CMap::mergeArray($this->_defaultOptions, $this->options);
		if ( !isset($this->options['target']) )
		{
			$this->options['target'] = '#'. $this->targetHtmlOptions['id'];
		}

		if ( !empty($this->data) )
		{
			$this->options['hints'] = $this->data;
			$this->options['number'] = count($this->data);
			$this->options['targetType'] = 'hint';
		}
		
		// Global default options
		if ( ! $cs->isScriptRegistered(__CLASS__.'#defaults', CClientScript::POS_HEAD) )
		{
			$js_default = '';
			$defaultOptions = $this->_defaultOptions;
			unset($defaultOptions['target']);
			unset($defaultOptions['score']);
			foreach ( $defaultOptions as $id_option => $val_option ) 
			{
				$js_default .= "$.fn.raty.defaults.". $id_option ."=". CJavaScript::encode($val_option) ."; \n";
			}
			$cs->registerScript(__CLASS__.'#defaults', $js_default, CClientScript::POS_HEAD);
		}
		
		// Javascript needed for this raty widget
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile("{$this->assetsUrl}/js/jquery.raty" . (YII_DEBUG ? '' : '.min') . ".js");		
		$cs->registerScript(__CLASS__ .'#'. $this->id, "jQuery('#{$this->htmlOptions['id']}').raty(". CJavaScript::encode($this->options) ."); jQuery('{$this->options['target']}').hide();", CClientScript::POS_READY);
	}
}