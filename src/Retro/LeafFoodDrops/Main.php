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
use pocketmine\item\StringToItemParser;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

final class Main extends PluginBase implements Listener{

    protected function onEnable() : void{
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder(), 0777, true);
        }

        if(!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveDefaultConfig();
        }else{
            $this->reloadConfig();
        }

        $pm = $this->getServer()->getPluginManager();

        $pm->registerEvent(BlockBreakEvent::class, function(BlockBreakEvent $e) : void{
            if($e->isCancelled()) return;
            if(!(bool)$this->getConfig()->get("enabled", true)) return;

            $block = $e->getBlock();
            $worldName = $block->getPosition()->getWorld()->getFolderName();

            if(!$this->isWorldEnabled($worldName)) return;

            if(!$this->isLeafHeuristic($block, $e->getDrops())) return;

            $chance = (float)$this->getConfig()->get("chance", 0.08);
            $chance = max(0.0, min(1.0, $chance));

            $roll = mt_rand(1, 1000000) / 1000000;
            if($roll > $chance) return;

            $food = $this->rollFoodFromConfig();
            if($food === null) return;

            $min = (int)$this->getConfig()->get("count_min", 1);
            $max = (int)$this->getConfig()->get("count_max", 1);
            if($min < 1) $min = 1;
            if($max < $min) $max = $min;

            $food->setCount(mt_rand($min, $max));

            $player = $e->getPlayer();

            if($player->isCreative()){
                if(!(bool)$this->getConfig()->get("creative_drop", true)) return;
                $pos = $block->getPosition()->add(0.5, 0.5, 0.5);
                $block->getPosition()->getWorld()->dropItem($pos, $food);
                return;
            }

            $drops = $e->getDrops();
            $drops[] = $food;
            $e->setDrops($drops);

        }, EventPriority::HIGHEST, $this, false);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() !== "lf") return false;

        if(!$sender->hasPermission("leafdrops.admin")){
            $sender->sendMessage($this->t("no_permission"));
            return true;
        }

        if(count($args) < 2){
            $sender->sendMessage($this->t("usage"));
            return true;
        }

        $mode = strtolower((string)$args[0]);
        $world = (string)$args[1];

        if($mode !== "on" && $mode !== "off"){
            $sender->sendMessage($this->t("usage"));
            return true;
        }

        $enabled = ($mode === "on");
        $this->setWorldEnabled($world, $enabled);

        $sender->sendMessage($this->t("toggled", [
            "world" => $world,
            "state" => $enabled ? $this->t("state_on") : $this->t("state_off")
        ]));
        return true;
    }

    private function isWorldEnabled(string $worldName) : bool{
        return (bool)$this->getConfig()->getNested("worlds." . $worldName, true);
    }

    private function setWorldEnabled(string $worldName, bool $enabled) : void{
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder(), 0777, true);
        }
        $this->getConfig()->setNested("worlds." . $worldName, $enabled);
        $this->getConfig()->save();
    }

    private function rollFoodFromConfig() : ?Item{
        $foods = (array)$this->getConfig()->get("foods", []);
        if(count($foods) <= 0){
            return null;
        }

        $entries = [];
        $total = 0;

        foreach($foods as $id => $weight){
            $id = strtolower(trim((string)$id));
            $w = (int)$weight;
            if($id === "" || $w <= 0) continue;

            $item = StringToItemParser::getInstance()->parse($id);
            if($item === null) continue;

            $entries[] = [$id, $w];
            $total += $w;
        }

        if($total <= 0) return null;

        $pick = mt_rand(1, $total);
        $acc = 0;

        foreach($entries as [$id, $w]){
            $acc += $w;
            if($pick <= $acc){
                return StringToItemParser::getInstance()->parse((string)$id);
            }
        }

        return null;
    }

    private function t(string $key, array $vars = []) : string{
        $lang = strtolower((string)$this->getConfig()->get("language", "en"));
        if($lang !== "es" && $lang !== "en") $lang = "en";

        $messages = (array)$this->getConfig()->get("messages", []);
        $pack = (array)($messages[$lang] ?? []);
        $msg = (string)($pack[$key] ?? "");

        if($msg === ""){
            $fallback = (array)($messages["en"] ?? []);
            $msg = (string)($fallback[$key] ?? $key);
        }

        foreach($vars as $k => $v){
            $msg = str_replace("{" . $k . "}", (string)$v, $msg);
        }
        return $msg;
    }

    private function isLeafHeuristic(Block $block, array $defaultDrops) : bool{
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
