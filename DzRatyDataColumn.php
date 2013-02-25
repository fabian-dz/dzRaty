<?php
/**
 * DzRatyDataColumn class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://dezero.es/
 * @copyright Copyright &copy; 2013 Dezero
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package extensions.DzRaty
 */
Yii::import('zii.widgets.grid.CDataColumn');

/**
 * DzRatyDataColumn provides a data column for CGridView widget with jQuery Raty plugin support
 * @see http://wbotelhos.com/raty
 */
class DzRatyDataColumn extends CDataColumn
{
	/**
	 * jQuery Raty options for cells
	 *
	 * @var array
	 */
	public $options = array();


	/**
	 * jQuery Raty options for filter
	 *
	 * @var array
	 */
	public $filterOptions = array();
	
		
	/**
	 * Cell identificator 
	 * 
	 * @var string
	 */
	private $_cell_id;
	
	/**
	 * Initializes the column
	 */
	public function init()
	{
		parent::init();

		// Register current widget path alias.
		if (Yii::getPathOfAlias('dzraty') === FALSE)
		{
			Yii::setPathOfAlias('dzraty', realpath(dirname(__FILE__) . '/..'));
		}


		// jQuery Raty options for cell data		
		$default_options = array(
			'readOnly' => TRUE,
			'score' => 'js:function(){ return $(this).data("score"); }'
		);
		$this->options = CMap::mergeArray($this->options, $default_options);
		
		// jQuery Raty options for filter
		if ( empty($this->filterOptions) )
		{
			$this->filterOptions = $this->options;
			unset($this->filterOptions['score']);
		}
				
		// Fire event change manually for refresh CGridView
		$this->filterOptions['click'] = 'js:function(score,event){$("#"+$(this).data("target")).change();}';
		$this->filterOptions['readOnly'] = FALSE;
		$this->filterOptions['targetType'] = 'number';
		$this->filterOptions['targetKeep'] = TRUE;
		
		if ( isset($this->filter[1]['options']) )
		{
			$this->filterOptions = CMap::mergeArray($this->filter[1]['options'], $this->filterOptions);
		}

		// Run some javascript code for "afterAjaxUpdate" CGridView option
		$this->registerClientScript();
	}
	
	/**
	 * Renders the data cell content. This method evaluates value or name and renders the result.
	 */
	public function renderDataCellContent($row, $data)
	{
		$this->_cell_id = 'raty-' .$this->id.'-'. $row;
		$this->type = 'raw';
		$this->value = '$this->grid->controller->widget("dzraty.DzRaty", array(
			"model" => $data,
			"attribute" => "'. $this->name .'",
			"htmlOptions" => array(
				"id" => "'. $this->_cell_id .'",
				"data-score" => $data->'. $this->name .',
				"class" => "raty-cell"
			),
			"options" => '. var_export($this->options, TRUE) .'
		), TRUE)';
		
		parent::renderDataCellContent($row, $data);
	}
	
	
	/**
	 * Renders the filter cell
	 */
	public function renderFilterCell()
	{
		$this->filterHtmlOptions = CMap::mergeArray(
			array('class' => $this->name. '_filter'),
			$this->filterHtmlOptions
		);
		parent::renderFilterCell();
	}
	
	
	/**
	 * Renders the filter cell content
	 */
	function renderFilterCellContent()
	{
		echo DzHtml::openTag("div", array('class' => 'filter-wrapper raty-filter-wrapper'));
		
		// Run the widget & capture the output
		if ( $this->filter !== NULL AND is_array($this->filter) AND count($this->filter) == 2 )
		{
			$this->filter[1]['options'] = $this->filterOptions;
			$this->filter = $this->grid->controller->widget($this->filter[0], $this->filter[1], TRUE);
			/*
			$this->filter = $this->grid->controller->widget('ext.DzRaty.DzRaty', array(
				'model' => $this->grid->filter,
				'attribute' => $this->name,
				'options' => $this->filterOptions
			), TRUE);
			*/
		}
		parent::renderFilterCellContent();
		echo '</div>';
	}
	

	/**
	 * Register script that will handle its behavior
	 */
	public function registerClientScript()
	{	
		// Javascript function invoked if an AJAX call occurs in CGridView
		$cs = Yii::app()->getClientScript();
		if ( ! $cs->isScriptRegistered(__CLASS__.'#afterAjaxUpdate', CClientScript::POS_HEAD) )
		{
			$js_default = 'function dzRatyUpdate() {
				jQuery(".raty-cell").raty('. CJavaScript::encode($this->options) .').each(function(){
					jQuery("#"+jQuery(this).data("target")).hide();
				});
				jQuery(".raty-filter-wrapper .raty-icons").each(function(){
					var $this = jQuery(this), raty_options = '. CJavaScript::encode($this->filterOptions) .';
					raty_options.target = "#"+$this.data("target");
					var $target = jQuery(raty_options.target);
					raty_options.score = $target.val();
					$this.raty(raty_options);
					console.log(raty_options);
					$target.hide();
				});
			}';
			
			// Define dzRatyUpdate() javascript function
			$cs->registerScript(__CLASS__.'#afterAjaxUpdate', $js_default, CClientScript::POS_HEAD);
			
			// Run dzRatyUpdate() javascript function the first time when DOM is loaded
			$cs->registerScript(__CLASS__.'#init', 'dzRatyUpdate();', CClientScript::POS_READY);
		}
	}
}
