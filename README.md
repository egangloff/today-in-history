# 📅 TodayInHistory – WordPress Plugin

[![WordPress](https://img.shields.io/badge/WordPress-%3E=5.0-blue)](https://wordpress.org/)  
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)  
[![PHP](https://img.shields.io/badge/PHP-%3E=7.4-777BB4)](https://www.php.net/)

**TodayInHistory** is a WordPress plugin that displays historical events, notable births, and deaths for a given date (defaults to today).  
It uses the public **"On This Day" API** provided by [byabbe.se](https://byabbe.se/on-this-day/), with server-side caching and customizable settings.

---

## ✨ Features
- **Shortcode & Gutenberg Block** support  
- Displays:  
  - 🗓 Historical events  
  - 👶 Notable births  
  - ⚰ Notable deaths  
- **Server-side caching** to reduce API calls  
- Light/Dark/Auto theme support  
- Translatable interface (`.po/.mo` files)  
- Works with **any WordPress theme**

---

## 🔌 Shortcode Examples

Here are several ways you can use the `[tih]` shortcode in your posts, pages, or widgets.

[tih]
→ Displays today’s events (default type: events).

[tih type="births"]
→ Displays all notable births for today.

[tih type="births" limit="5"]
→ Displays the 5 most recent notable births for today.

[tih type="deaths" month="8" day="14"]
→ Displays notable deaths for August 14 (any year).

[tih type="events" month="12" day="25" limit="3"]
→ Displays the 3 most recent events for December 25 (any year).

[tih type="events" limit="10" month="1" day="1"]
→ Displays 10 historical events that happened on January 1st.

[tih type="births" month="5" day="20" limit="8"]
→ Displays 8 notable births for May 20.

[tih type="deaths" limit="2"]
→ Displays only the 2 most recent deaths for today.

[tih type="events" limit="3"]
[tih type="births" limit="3"]
[tih type="deaths" limit="3"]
→ Displays 3 events, 3 births, and 3 deaths for today (side by side).