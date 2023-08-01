<?php
/**
 * @author MagentoBrain Team
 * @copyright Copyright (c) 2022 MagentoBrain (https://www.magentobrain.com)
 * @package MagentoBrain_Core
 */


namespace MagentoBrain\Core\Plugin\BackendMenu;

use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu;
use MagentoBrain\Core\Helper\Extensions;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Backend\Model\Menu\Item;
use Magento\Backend\Model\Menu\ItemFactory;
use Magento\Backend\Model\Menu\Filter\IteratorFactory;
use MagentoBrain\Core\Model\Logger;
use MagentoBrain\Core\Helper\Config as MagentoBrainConfig;

class Builder
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var MagentoBrainConfig
     */
    private $magentobrainConfig;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var Extensions
     */
    private $extensionHelper;

    /**
     * @var Structure
     */
    private $configStructure;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        Config $config,
        MagentoBrainConfig $magentobrainConfig,
        ProductMetadataInterface $productMetadata,
        Extensions $extensionHelper,
        Structure $configStructure,
        ItemFactory $itemFactory,
        IteratorFactory $iteratorFactory,
        Logger $logger
    ) {
        $this->config = $config;
        $this->magentobrainConfig = $magentobrainConfig;
        $this->productMetadata = $productMetadata;
        $this->extensionHelper = $extensionHelper;
        $this->configStructure = $configStructure;
        $this->itemFactory = $itemFactory;
        $this->iteratorFactory = $iteratorFactory;
        $this->logger = $logger;
    }

    /**
     * @param $subject
     * @param Menu $menu
     * @return Menu
     */
    public function afterGetResult($subject, Menu $menu)
    {
        return $menu->get('MagentoBrain_Core::menu') ? $this->buildMenu($menu):  $menu;
    }

    /**
     * @param Menu $menu
     * @return Menu
     */
    private function buildMenu(Menu $menu)
    {
        $menuWithoutMagentoBrain = $this->isRemoveMagentoBrainTab($menu);
        if ($menuWithoutMagentoBrain) {
            return $menuWithoutMagentoBrain;
        }
        try {
            $configItems = $this->getItemsFromConfig();
            $extensionData = [];
            $magentobrainExtensions = $this->extensionHelper->getMagentoBrainExtensions(true);
            foreach ($magentobrainExtensions as $extCode) {
                if (!empty($configItems[$extCode]['label']) && $configItems[$extCode]['label']) {
                    $label = $configItems[$extCode]['label'];
                } elseif ($extInfo = $this->extensionHelper->getExtInfo($extCode) && !empty($extInfo['description'])) {
                    $label = $extInfo['description'];
                } else {
                    $label = explode('_', $extCode)[1];
                }
                $extensionData[$label] = $extCode;
            }

            $this->render($extensionData, $configItems, $menu);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $menu;
    }

    /**
     * @param array $extensionData
     * @param array $configItems
     * @param Menu $menu
     * @throws \Exception
     */
    private function render($extensionData, $configItems, Menu $menu)
    {
        $menuItems = $this->getMenuItems($this->config->getMenu());
        foreach ($extensionData as $label => $extCode) {
            $renderedItems = [];
            if (!empty($menuItems[$extCode])) {
                $renderedItems = array_merge($renderedItems, $this->getRenderedItemsByExtension($menuItems[$extCode], $menu));
            }

            if (!empty($configItems[$extCode]['id'])) {
                $renderedItems[] = $this->renderItem(
                    $extCode . '_magentobrainMenu',
                    $extCode,
                    __('🔧 Configuration')->render(),
                    'MagentoBrain_Core::menu',
                    'adminhtml/system_config/edit/section/' . $configItems[$extCode]['id']
                );
            }

            if ($renderedItems) {
                $itemId = $extCode . 'magentobrainMenu';
                /** @var Item $mainItem */
                $mainItem = $this->itemFactory->create(['data' => [
                    'id' => $itemId,
                    'title' => $label,
                    'module' => $extCode,
                    'resource' => 'MagentoBrain_Core::menu'
                ]]);
                $menu->add($mainItem, 'MagentoBrain_Core::menu', 1);
                foreach ($renderedItems as $item) {
                    $menu->add($item, $itemId);
                }
            }
        }
    }

    /**
     * @param Menu $menu
     * @return bool|Menu
     */
    private function isRemoveMagentoBrainTab(Menu $menu)
    {
        if (!$this->magentobrainConfig->getMenuEnable()
            || version_compare($this->productMetadata->getVersion(), '2.2', '<')
        ) {
            $menu->remove('MagentoBrain_Core::menu');
            return $menu;
        }

        return false;
    }

    /**
     * @param array $menuItems
     * @param Menu $menu
     * @return array
     */
    private function getRenderedItemsByExtension($menuItems, Menu $menu)
    {
        $newItems = [];
        foreach ($menuItems as $itemId) {
            $item = $menu->get($itemId);
            if ($item) {
                $item = $item->toArray();
                if (isset($item['id']) && isset($item['title'])  && isset($item['resource']) && isset($item['action'])) {
                    $extension = empty($item['module']) ? explode('::', $item['resource'])[0] : $item['module'];
                    $newItems[] = $this->renderItem(
                        $item['id'] . '_magentobrainMenu',
                        $extension,
                        $item['title'],
                        $item['resource'],
                        $item['action']
                    );
                }
            }
        }

        return $newItems;
    }

    /**
     * @param string $id
     * @param string $extension
     * @param string $title
     * @param string $resource
     * @param string $url
     * @return Item|null
     */
    private function renderItem($id, $extension, $title, $resource, $url)
    {
        try {
            $item = $this->itemFactory->create(['data' => [
                'id' => $id,
                'module' => $extension,
                'title' => $title,
                'action' => $url,
                'resource' => $resource
            ]]);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            $item = null;
        }

        return $item;
    }

    /**
     * @param Menu $menu
     * @return array
     */
    private function getMenuItems(Menu $menu)
    {
        $menuItems = [];
        foreach ($this->generateMenuItems($menu) as $item) {
            $name = explode('::', $item)[0];
            if (!isset($menuItems[$name])) {
                $menuItems[$name] = [];
            }
            $menuItems[$name][] = $item;
        }

        return $menuItems;
    }

    /**
     * @param Menu $menu
     * @return array
     */
    private function generateMenuItems(Menu $menu)
    {
        $menuItems = [];
        foreach ($this->iteratorFactory->create(['iterator' => $menu->getIterator()]) as $menuItem) {
            /** @var Item $menuItem */
            if ($this->validateMenuItem($menuItem)) {
                $menuItems[] = $menuItem->getId();
            }
            if ($menuItem->hasChildren()) {
                $menuItems = array_merge($menuItems, $this->generateMenuItems($menuItem->getChildren()));
            }
        }

        return $menuItems;
    }

    /**
     * @return array
     */
    private function getItemsFromConfig()
    {
        $configItems = [];
        foreach ($this->configStructure->getTabs() as $tab) {
            if ($tab->getId() == 'magentobrain_extensions') {
                foreach ($tab->getChildren() as $item) {
                    $itemData = $item->getData('resource');
                    if (!empty($itemData['id']) && $itemData['id'] && !empty($itemData['resource'])) {
                        $name = explode('::', $itemData['resource'])[0];
                        $configItems[$name] = $itemData;
                    }
                }
                break;
            }
        }

        return $configItems;
    }

    /**
     * @param Item $menuItem
     * @return bool
     */
    private function validateMenuItem(Item $menuItem)
    {
        return $menuItem->getAction()
            && strpos($menuItem->getAction(), 'system_config') === false
            && strpos($menuItem->getId(), 'MagentoBrain') !== false
            && strpos($menuItem->getId(), 'MagentoBrain_Core') === false;
    }
}
