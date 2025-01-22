# Dictionary

A block that allows students to look up definitions from the Merriam-Webster
dictionary without having to leave Moodle.

In order to use this plugin you'll need to go to https://dictionaryapi.com and
create an account. When creating the account, request API keys for the 
Collegiate dictionary and the thesaurus. (These are the only two supported
dictionaries at this time).

Currently the dictionary only returns the "shortdef" entries from the API.
The description of the shortdef tag is here: https://dictionaryapi.com/products/json#sec-2.shortdef

We strongly recommend after you install the plugin you compare the definitions
from the plug to the definitions on the Merriam-Webster main site (https://www.merriam-webster.com/)
to make sure it will meet your needs.

**Change Log**

2025011700
- Some formatting tweaks to make Moodle happy.

2024121100
- Fix an error when near antonyms aren't included.

2021072101
- More code tweaks.

2021072100
- Various tweaks to comply with Moodle coding guidelines.

2021052100
- Some code cleanup. First public release

2021050601
- Store the dictionary used in the session variable to it sticks on subsequent pages.

2021050600
- Added support for a thesaurus

2021042900
- Initial Release

- Multiple dictionary support.
- Full definiations (instead of abbreviated).
- Pronunciation and sounds.
