# 🍃 LeafFoodDrops

[![Poggit-CI](https://poggit.pmmp.io/shield.state/LeafFoodDrops)](https://poggit.pmmp.io/p/LeafFoodDrops)
[![API 5](https://img.shields.io/badge/PocketMine--MP-API%205.x-green)](#)

**LeafFoodDrops** is a lightweight and fun plugin for **PocketMine-MP 5** that adds a small survival twist:  
when players break leaf blocks, they have a random chance to receive different types of **RAW food**.  
Perfect for **SkyBlock**, **Survival**, or **Adventure** servers that want to make exploration more rewarding.

---

## 🌱 Features
- Random **RAW food drops** when breaking leaves (player-break only, no decay)
- Fully compatible with **PocketMine-MP 5**
- Lightweight and performance-friendly
- Simple configuration file (`config.yml`)
- Per-world enable/disable with commands
- Optional multi-language messages (EN default, ES available)
- No dependencies required

---

## ⚙️ Configuration
When the plugin starts, it automatically creates a configuration file like this:

```yaml
enabled: true

# NOTES:
# - Plugin language:
#   - en = English (default)
#   - es = Spanish
language: en

# NOTES:
# - Drop chance (0.0 to 1.0)
#   - 0.03 = 3% (low)
#   - 0.06 = 6% (normal)
#   - 0.10 = 10% (high)
chance: 0.06

# NOTES:
# - Amount dropped per successful roll
count_min: 1
count_max: 1

# NOTES:
# - If the player is in creative:
#   - true = drop the item on the ground
#   - false = drop nothing
creative_drop: true

# NOTES:
# - Enable/disable per world:
#   - If a world is NOT listed here, it defaults to ON (true)
#   - Commands:
#     - /lf on <world>
#     - /lf off <world>
worlds:
  world: true
  lobby: false

# NOTES:
# - Foods (RAW) with weights:
#   - Higher weight = higher probability
#   - You can use "minecraft:beef" or "beef"
foods:
  minecraft:apple: 8
  minecraft:beef: 4
  minecraft:chicken: 4
  minecraft:porkchop: 4
  minecraft:mutton: 3
  minecraft:rabbit: 2
  minecraft:potato: 4
  minecraft:carrot: 5
  minecraft:beetroot: 4

# NOTES:
# - Plugin messages per language
# - Placeholders:
#   - {world} = world name
#   - {state} = ON/OFF
messages:
  en:
    no_permission: "§cYou don't have permission."
    usage: "§cUsage: §f/lf <on|off> <world>"
    toggled: "§aLeafFoodDrops in §f{world} §a=> {state}"
    state_on: "§aON"
    state_off: "§cOFF"
  es:
    no_permission: "§cNo tienes permiso."
    usage: "§cUso: §f/lf <on|off> <mundo>"
    toggled: "§aLeafFoodDrops en §f{world} §a=> {state}"
    state_on: "§aON"
    state_off: "§cOFF"
```
🧩 Commands

/lf on <world> Enable LeafFoodDrops in a world

/lf off <world> Disable LeafFoodDrops in a world

Permission: leafdrops.admin (default: op)

📦 Installation

Download the latest .phar from Poggit

Place it inside the plugins/ folder of your PocketMine-MP 5 server

Restart the server and edit plugin_data/LeafFoodDrops/config.yml

🧠 Author

Developed by JesusHR1k (RetroMC Team)
Created for the PocketMine-MP 5 community ❤️

🧾 License

This project is open-source under the MIT License.
Feel free to use or modify it for your own server
