FROM php:8.1-fpm
RUN apt-get update && apt-get install -y libc-client-dev libkrb5-dev && rm -r /var/lib/apt/lists/*
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap mysqli
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" "$PHP_INI_DIR/php.ini"
RUN sed -i "s/max_execution_time = 30/max_execution_time = 3000/" "$PHP_INI_DIR/php.ini"
RUN sed -i "s/max_input_time = 30/max_input_time = 3000/" "$PHP_INI_DIR/php.ini"
RUN sed -i "s/memory_limit = 128M/memory_limit = 512M/" "$PHP_INI_DIR/php.ini"
RUN sed -i "s/post_max_size = 8M/post_max_size = 64M/" "$PHP_INI_DIR/php.ini"
RUN sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 64M/" "$PHP_INI_DIR/php.ini"
