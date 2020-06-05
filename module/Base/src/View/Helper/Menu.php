<?php
namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * This view helper class displays a menu bar.
 */
class Menu extends AbstractHelper
{
    /**
     * Menu items array.
     * @var array
     */
    protected $items = [];

    /**
     * Active item's ID.
     * @var string
     */
    protected $activeItemId = '';

    /**
     * Active list item's ID.
     * @var string
     */
    protected $activeListItemId = '';

    /**
     * Constructor.
     * @param array $items Menu items.
     */
    public function __construct($items=[])
    {
        $this->items = $items;
    }

    /**
     * Sets menu items.
     * @param array $items Menu items.
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * Sets ID of the active items.
     * @param string $activeItemId
     */
    public function setActiveItemId($activeItemId)
    {
        $this->activeItemId = $activeItemId;
    }

    /**
     * Sets ID of the active list items.
     * @param string $activeListItemId
     */
    public function setActiveListItemId($activeListItemId)
    {
        $this->activeListItemId = $activeListItemId;
    }

    /**
     * Renders the menu.
     * @return string HTML code of the menu.
     */
    public function render()
    {

        if (count($this->items)==0)
            return ''; // Do nothing if there are no items.

        $result = '<nav class="navbar fixed-top navbar-expand-lg">';

            $result .='<button class="navbar-toggler collapsed"
                               type="button"
                               data-toggle="collapse"
                               data-target="#navbar-csec"
                               aria-controls="navbar-csec"
                               aria-expanded="false"
                               aria-label="Expandir menu">';

                $result .= '<span></span>
                            <span></span>
                            <span></span>';

            $result .= '</button>';


            $result .= '<a class="navbar-brand" href="'.\Base\Module::ROOT_PATH.'">';
                $result .= '<strong>WINDEL</strong>';
            $result .= '</a>';


            $result .= '<div class="collapse navbar-collapse" id="navbar-csec">';
                $result .= '<ul class="navbar-nav mr-auto">';
                            foreach ($this->items as $item) {
                                if(!isset($item['float']) || $item['float']=='left')
                                    $result .= $this->renderItem($item);
                            }
                $result .= '</ul>';
                $result .= '<ul class="navbar-nav navbar-right">';
                            foreach ($this->items as $item) {
                                if(isset($item['float']) && $item['float']=='right')
                                    $result .= $this->renderItem($item);
                            }
                $result .= '</ul>';
            $result .= '</div>';

        $result .= '</nav>';

        return $result;

    }

    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @return string HTML code of the item.
     */
    protected function renderItem($item)
    {
        $id = isset($item['id']) ? $item['id'] : '';
        $isActive = ($id==$this->activeItemId);

        $label = isset($item['label']) ? $item['label'] : '';
        $result = '';

        $escapeHtml = $this->getView()->plugin('escapeHtml');

        if (isset($item['dropdown'])) {

            $dropdownItems = $item['dropdown'];

            $result .= '<li class="nav-item dropdown '.($isActive?'active':'').'">';
                $target = $item['id'];

                $result .= '<a href="#"
                               class="nav-link dropdown-toggle"
                               data-toggle="dropdown"
                               role="button"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false"
                               id="'.$target.'">';
                    $result .= $escapeHtml($label) . ' <b class="caret"></b>';
                $result .= '</a>';

                $result .= '<div class="dropdown-menu" aria-labelledby="'.$target.'">';
                foreach ($dropdownItems as $item) {

                    $link = isset($item['link']) ? $item['link'] : '#';
                    $label = isset($item['label']) ? $item['label'] : '';

                    $class = $label==$this->activeListItemId ? 'dropdown-item active' : 'dropdown-item';

                    $result .= '<a class="'.$class.'" href="'.$escapeHtml($link).'">'.$label.'</a>';
                }
                $result .= '</div>';
            $result .= '</li>';

        } else {
            $link = isset($item['link']) ? $item['link'] : '#';

            $result .= $isActive ? '<li class="nav-item active">' : '<li class"nav-item">';
                $result .= '<a class="nav-link" href="'.$escapeHtml($link).'">'.$escapeHtml($label).'</a>';
            $result .= '</li>';
        }

        return $result;
    }
}
