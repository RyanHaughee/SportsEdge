# include init script
source('init.R')

# Check to see if packages are installed. # Install them if they are not
check.packages <- function(pkg){
  new.pkg <- pkg[!(pkg %in% installed.packages()[, "Package"])]
  if (length(new.pkg))
    install.packages(new.pkg, dependencies = TRUE)
  sapply(pkg, require, character.only = TRUE, quietly = TRUE)
}
packages<-c("Rtools","tidyverse","ggrepel","nflreadr","nflplotR")
check.packages(packages)

# Library packages
library(tidyverse)
library(dplyr)
library(readr)
library(stringr)
library(ggrepel)
library(nflreadr)
library(nflplotR)

# get connection to platform database
con <- get_db()
db_table <- "nfl_players"

# NFL players
nfl_load_players <- load_players(
  file_type = getOption("nflread.prefer", default = "rds")
) %>%
  select(
    -season
  )

# Week to week NFL rosters
nfl_load_rosters <- load_rosters(
  seasons = 2023,
  file_type = getOption("nflread.prefer", default = "rds")
)

# Remove common columns from nfl_load_rosters_weekly
nfl_roster_players <- nfl_load_rosters %>%
  distinct(    
    gsis_id,
    espn_id,
    sportradar_id,
    yahoo_id,
    rotowire_id,
    pff_id,
    pfr_id,
    fantasy_data_id,
    sleeper_id
)

# Additional player IDs
ff_playerids <- load_ff_playerids()

# Clean df
ff_playerids <- ff_playerids %>%
  distinct(
    gsis_id,
    mfl_id,
    fantasypros_id,
    nfl_id,
    fleaflicker_id,
    cbs_id,
    rotoworld_id,
    ktc_id,
    stats_id,
    swish_id
  )

# Join together data frames
nfl_players <- left_join(
  nfl_load_players, nfl_roster_players, by = "gsis_id"
)

# Complete player data frame
nfl_players <- left_join(
  nfl_players, ff_playerids, by = "gsis_id"
) %>%
  filter(
    !is.na(gsis_id) & gsis_id != ""
  ) %>%
  distinct(
    gsis_id,
    status,
    display_name,
    first_name,
    last_name,
    suffix,
    short_name,
    current_team_id,
    espn_id,
    sportradar_id,
    yahoo_id,
    rotowire_id,
    pff_id,
    pfr_id,
    fantasy_data_id,
    sleeper_id,
    esb_id,
    gsis_it_id,
    smart_id,
    mfl_id,
    fantasypros_id,
    nfl_id,
    fleaflicker_id,
    cbs_id,
    rotoworld_id,
    ktc_id,
    stats_id,
    swish_id,
    birth_date,
    college_name,
    college_conference,
    position_group,
    position,
    jersey_number,
    height,
    weight,
    years_of_experience,
    entry_year,
    rookie_year,
    draft_club,
    status_description_abbr,
    status_short_description,
    draft_number,
    uniform_number,
    draft_round,
    headshot
)

# Enable local infile loading for the RMySQL connection
dbGetQuery(con, "SET GLOBAL local_infile = 'ON'")

# Truncate the table
dbExecute(con, paste("TRUNCATE TABLE", db_table))

# Use dbWriteTable to insert the entire data frame
dbWriteTable(
  conn = con,
  name = db_table,
  value = nfl_players, # UPDATE
  append = TRUE,  # Append data to the existing table
  overwrite = FALSE,  # Do not overwrite the table
  row.names = FALSE,  # Do not include row names as a separate column
  convert = "NULL"  # Specify how NA values should be handled
)

# Filter non-numeric column names
non_numeric_cols <- names(nfl_players)[!sapply(nfl_players, is.numeric)] # UPDATE

# SQL query to update empty strings and NA values to NULL for each non-numeric column
for (col in non_numeric_cols) {
  query <- sprintf("UPDATE %s SET %s = NULL WHERE %s = '' OR %s IS NULL;", db_table, col, col, col)
  dbExecute(con, query)
}

# Close the connection
dbDisconnect(con)


