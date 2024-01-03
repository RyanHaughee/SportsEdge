# include init script
source('init.R')

# Check to see if packages are installed. # Install them if they are not
check.packages <- function(pkg){
  new.pkg <- pkg[!(pkg %in% installed.packages()[, "Package"])]
  if (length(new.pkg))
    install.packages(new.pkg, dependencies = TRUE)
  sapply(pkg, require, character.only = TRUE, quietly = TRUE)
}
packages<-c("Rtools","tidyverse","ggrepel","nflreadr","nflfastR","nflplotR","gsisdecoder")
check.packages(packages)

# Library packages
library(tidyverse)
library(dplyr)
library(readr)
library(stringr)
library(ggrepel)
library(nflreadr)
library(nflfastR)
library(nflplotR)
library(gsisdecoder)

# get connection to platform database
con <- get_db()
db_table <- "nfl_off_nextgen_stats"

# No scientific notation
options(scipen = 9999)

# Next-Gen-Stats passing stats
nfl_off_nextgen_pass <- load_nextgen_stats(
  seasons = 2023,
  stat_type = "passing",
  file_type = getOption("nflreadr.prefer", default = "rds")
) %>%
  filter(
    week != 0
  )

# Next-Gen-Stats receiving stats
nfl_off_nextgen_rec <- load_nextgen_stats(
  seasons = 2023,
  stat_type = "receiving",
  file_type = getOption("nflreadr.prefer", default = "rds")
) %>%
  filter(
    week != 0
  ) %>%
  rename(
    avg_targeted_air_yards = avg_intended_air_yards,
    percent_share_of_targeted_air_yards = percent_share_of_intended_air_yards
  )

# Next-Gen-Stats rushing stats
nfl_off_nextgen_rush <- load_nextgen_stats(
  seasons = 2023,
  stat_type = "rushing",
  file_type = getOption("nflreadr.prefer", default = "rds")
) %>%
  filter(
    week != 0
  )

# Assuming dfs is a list of your data frames (nfl_off_nextgen_pass, nfl_off_nextgen_rec, nfl_off_nextgen_rush)
dfs <- list(nfl_off_nextgen_pass, nfl_off_nextgen_rec, nfl_off_nextgen_rush)

# Get the common column names
common_cols <- Reduce(intersect, lapply(dfs, colnames))

# Create a new data frame with unique records based on common columns
unique_records <- unique(bind_rows(lapply(dfs, function(df) df %>% distinct(across(all_of(common_cols))))))

# Left join each data frame with unique_records
nfl_off_nextgen_stats <- left_join(unique_records, nfl_off_nextgen_pass, by = common_cols)
nfl_off_nextgen_stats <- left_join(nfl_off_nextgen_stats, nfl_off_nextgen_rec, by = common_cols)
nfl_off_nextgen_stats <- left_join(nfl_off_nextgen_stats, nfl_off_nextgen_rush, by = common_cols)

# Enable local infile loading for the RMySQL connection
dbGetQuery(con, "SET GLOBAL local_infile = 'ON'")

# Truncate the table
dbExecute(con, paste("TRUNCATE TABLE", db_table))

# Use dbWriteTable to insert the entire data frame
dbWriteTable(
  conn = con,
  name = db_table,
  value = nfl_off_nextgen_stats,
  append = TRUE,  # Append data to the existing table
  overwrite = FALSE,  # Do not overwrite the table
  row.names = FALSE,  # Do not include row names as a separate column
  convert = "NULL"  # Specify how NA values should be handled
)

# Filter non-numeric column names
non_numeric_cols <- names(nfl_off_nextgen_stats)[!sapply(nfl_off_nextgen_stats, is.numeric)]

# SQL query to update empty strings and NA values to NULL for each non-numeric column
for (col in non_numeric_cols) {
  query <- sprintf("UPDATE %s SET %s = NULL WHERE %s = '' OR %s IS NULL;", db_table, col, col, col)
  dbExecute(con, query)
}

# Close the connection
dbDisconnect(con)




