#EconomyS
## For PocketMine-MP 1.4 Core-Rewrite

Feel free to make a pull request!

## EconomyAPI commands

| Default command | Parameter | Description | Default Permission |
| :-----: | :-------: | :---------: | :-------: |
| /mymoney | | Shows your money | All |
| /mydebt | | Shows your debt | All |
| /takedebt | `<money>` | Borrows $`<money>` from plugin | `All` |
| /returndebt | `<money>` | Returns $`<money>` to plugin | `All` |
| /topmoney | `<page>` | Shows server's top money | `All` |
| /moneysave | | Saves data to your hardware | `Console` |
| /moneyload | | Loads data from your hardware | `Console` |
| /setmoney | `<player>` `<money>` | Sets `<player>`'s money to $`<money>` | `OP` `Console` |
| /economys | | Shows plugin which are using EconomyAPI | `All` |
| /givemoney | `<player>` `<money>` | Gives $`<money>` `<player>` | `OP` `Console` |
| /takemoney | `<player>` `<money>` | Takes $`<money>` from `<player>` | `OP` `Console` |
| /seemoney | `<player>` | Shows `<player>`'s money | `All` |
| /bank deposit | `<money>` | Deposit $`<money>` to your account | `All` |
| /bank withdraw | `<money>` | Withdraw $`<money>` from your account | `All` |
| /bank mymoney | | Shows your money from your account | `All` |
| /mystatus | | Shows your money status | `All` |
| /bankadmin takemoney | `<player>` `<money>` | Takes $`<money>` from `<player>`'s account | `OP` `Console` |
| /bankadmin givemoney | `<player>` `<money>` | Gives $`<money>` for `<player>`'s account | `OP` `Console` |


## EconomyAPI configuration

> File : `plugins/EconomyAPI/economy.properties`

| Key | Description | Available Value | Default Value |
| :-: | :---------: | :---------------: | :---------: |
| show-using-economy | Changes server name to `[EconomyS] SERVER NAME`   `on : Change` `off : Don't change` | `on` `off` | on | 
| once-debt-limit | Limits borrowing debt at once | `All integers` | 100 |
| debt-limit | Limits available debt | `All integers` | 500 |
| add-op-at-rank | Shows OP at top money rank    `on : Shows OP` `off : Don't shows OP` | `on` `off` | off |
| default-money | Sets default money | `All integers` | 1000 |
| default-debt | Sets default debt | `All integers` | 0 |
| time-for-increase-debt | Sets how long will take for debt increase | `All integers` | 10 |
| percent-of-increase-debt | Sets percentage of increasing debt | `All integers` | 5 |
| default-bank-money | Sets default bank money | `All integers` | 0 |
| time-for-increase-money | Sets how long will take for credit increase | `All integers` | 10 |
| bank-increase=money-rate | Sets percentage of increasing credit | `All integers` | 5 |
| debug | Money debugging preferences  `on : yes` `off : no` | `on` `off` | on |

## EconomyShop configuration

> File : `plugins/EconomyShop/shop.properties`

| Key | Description | Available Value | Default Value |
| :-: | :---------: | :-------------: | :-----------: |
| handler-priority | The priority of handling shop touches | Integer | 5 |

## How to create shop in EconomyShop

> File : `plugins/EconomyShop/ShopSign.yml`

This documentation is focused on default configuration.

| Line1 | Line2 | Line3 | Line4 |
| :---: | :---: | :---: | :---: |
| shop | `<price>` | `<item id or name>` | `<amount>` |

You must write all the parameters `integer` except for line 3.
The shop will created if the parameters are valid.

`Sell Center` in `EconomySell` can be created in same way. And also `Player's Shop` in `EconomyPShop`

## EconomyAirport and EconomyAirportPlus

> File : `%CONFIG_PATH%/DepartureSign.yml`
> File : `%CONFIG_PATH%/ArrivalSign.yml`
> File : `%CONFIG_PATH%/Identifier.yml`

This documentation is focused on default configuration.

| Line1 | Line2 | Line3 | Line4 |
| :---: | :---: | :---: | :---: |
| `<international | airport>` | `<arrival | departure>` | `<arrival:`Airport name` departure:`price`>` | `<arrival:`none` departure:`<target>`>` |

Example:

Line1: airport
Line2: departure
Line3: 10
Line4: onebone

Takes $10 to go to `onebone` airport

Line1: airport
Line2: arrival
Line3: onebone
Line4: 

Arrival sign : `onebone` airport

If there's no target airport to fly, it aborts riding a flight.

## For Developers

You can access to EconomyAPI by using `EconomyAPI::getInstance()`

example)
EconomyAPI::getInstance()->addMoney($player_instance, $amount);