# kanpaiplus

~~几行瞎糊的 PHP 服务端代码，~~ 与[“解除B站区域限制”脚本](https://greasyfork.org/scripts/25718)配合使用。  
Part of sckc.stream Project.

## 说明

- 可以用于共享主机（shared hosting）——没必要买一台 VPS
- 目前的代码相当粗糙，简单起见服务端与 bilibili 的通信并未完全复刻标准的请求
- 并没有做大规模用户测试
- 可能不适用于所有的正版内容（会慢慢添加代码）
- 解除区域限制的能力仍然受限于你的服务器位置（即如果你在香港服务器上部署了此代码，则只能看在香港可用的正版内容，并不是全区域解锁）
- 请记得修改 `config.sample.php` 中的内容并改名为 `config.php`

## 许可证

[AGPL-3.0](https://github.com/SerCom-KC/kanpaiplus/raw/master/LICENSE)
