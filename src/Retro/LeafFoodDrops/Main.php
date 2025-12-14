<?php
declare(strict_types=1);

namespace Retro\LeafFoodDrops;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\EventPriority;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Block;
use pocketmine\block\Leaves;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class Main extends PluginBase implements Listener {

    protected function onEnable() : void {
        $pm = $this->getServer()->getPluginManager();

        // SOLO cuando un jugador rompe el bloque (nada de decaimiento)
        $pm->registerEvent(BlockBreakEvent::class, function(BlockBreakEvent $e) : void {
            if($e->isCancelled()) return; // zona protegida u otro plugin cancela

            $block = $e->getBlock();
            if(!$this->isLeafHeuristic($block, $e->getDrops())) return;

            // Pool de comidas (compatibles con PMMP 5.37)
            $foods = [
                VanillaItems::APPLE(),
                VanillaItems::BREAD(),
                VanillaItems::STEAK(),            
                VanillaItems::COOKED_CHICKEN(),
                VanillaItems::COOKED_PORKCHOP(),
                VanillaItems::BAKED_POTATO(),
                VanillaItems::COOKIE(),
                VanillaItems::CARROT(),
            ];
            $food = $foods[array_rand($foods)];
            $food->setCount(mt_rand(1, 2));

            $player = $e->getPlayer();
            if($player->isCreative()){
                $pos = $block->getPosition()->add(0.5, 0.5, 0.5);
                $block->getPosition()->getWorld()->dropItem($pos, $food);
                return;
            }

            $drops = $e->getDrops();
            $drops[] = $food;
            $e->setDrops($drops);
        }, EventPriority::HIGHEST, $this, false);
    }

    /**
     * Detección robusta de hojas:
     * - instanceof Leaves
     * - nombre de clase con "Leaves/Leaf/Hojas/Hoja"
     * - nombre visible con "leaf/hoja/bush/arbusto"
     * - drops vanilla con SAPLING/APPLE/STICK
     */
    private function isLeafHeuristic(Block $block, array $defaultDrops) : bool {

        if($block instanceof Leaves){
            return true;
        }

        $short = strtolower((new \ReflectionClass($block))->getShortName());
        if(str_contains($short, "leaves") || str_contains($short, "leaf") || str_contains($short, "hojas") || str_contains($short, "hoja")){
            return true;
        }

        $visible = strtolower($block->getName());
        if(str_contains($visible, "leaf") || str_contains($visible, "hoja") || str_contains($visible, "bush") || str_contains($visible, "arbusto")){
            return true;
        }

        foreach($defaultDrops as $it){
            if(!$it instanceof Item) continue;
            $vn = strtoupper($it->getVanillaName());
            if($vn === "APPLE" || $vn === "STICK" || str_contains($vn, "SAPLING")){
                return true;
            }
        }

        return false;
    }
}