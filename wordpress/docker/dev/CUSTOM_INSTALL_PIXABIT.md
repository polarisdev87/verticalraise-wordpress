Mail Catcher
https://hub.docker.com/r/yappabe/mailcatcher/

This adds mailcatcher and makes it available on on port 1080.

Usage
Add the following to your docker-compose.yml file:

mailcatcher:
image: yappabe/mailcatcher
ports: - 1025:1025 - 1080:1080
Next, add this container to your php container's links.

You can now use mailcatcher as an smtp server, simply use 'mailcatcher', or whatever you named this container as host on port 1025.
