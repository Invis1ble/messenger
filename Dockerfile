FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
	  git=2 \
      unzip=6 \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . .
