# Query Tools

## Files Structure
Project has following file structure:
- apps/ - 
  - class.agent_app.php - app to test requests with User-Agent
  - class.tags_checker_app.php - app to test tags
- classes/  - there is used classes
  - class.logger.php - logger (for logging execution info)
  - class.curl_request.php - CurlRequest class (cUrl wrapper)
  - class.tags_checker.php - TagsChecker class
- pages/ - pages for testing requests
  - test1.html
  - test2.html
- tags_checker/ - in this folder placed entry point classes for tags_checker_app
  - logs/ - here is logs storage
  - fixtures/ - folder for .csv or other input data files
  - tags_checker.php - entry point (runner itself)
- user_agent/ 
  - logs/ - here is logs storage
  - user_agent.php - entry point (runner itself)

## Installation 
To use this scripts please, clone this repo to your local machine. 

## How to run
To run **tags_checker_app**:
```
cd ~/path/to/cloned/repo/tags_checker && php tags_checker.php
```

To run **agent_app**:
```
cd ~/path/to/cloned/repo/user_agent && php user_agent.php
```
  
## Logs 
Logging system has been implemented in apps. Feel free to use them in case of problems.

# Demo


