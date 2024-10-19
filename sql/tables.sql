/*** audio server ***/
/* database structure */

--
-- table structure for table `album`
--

CREATE TABLE `album` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 NOT NULL,
  `artist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `artist`
--

CREATE TABLE `artist` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `playlist_party`
--

CREATE TABLE `playlist_party` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  `votes_total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `playlist_track`
--

CREATE TABLE `playlist_track` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `remote`
--

CREATE TABLE `remote` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 NOT NULL,
  `track_id` int(11) DEFAULT NULL,
  `position` double DEFAULT NULL,
  `state` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `identifier` text CHARACTER SET utf8 NOT NULL,
  `value` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `track`
--

CREATE TABLE `track` (
  `id` int(11) NOT NULL,
  `path` text CHARACTER SET utf8mb4 NOT NULL,
  `title` text CHARACTER SET utf8 NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL,
  `track_number` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `cover` text CHARACTER SET utf8,
  `length` bigint(20) DEFAULT NULL,
  `genre` text CHARACTER SET utf8 NOT NULL,
  `inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- table structure for table `download`
--

CREATE TABLE `download` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  `user` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `client` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- indexes for the table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- indexes for the table `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`id`);

--
-- indexes for the table `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`id`);

--
-- indexes for the table `playlist_party`
--
ALTER TABLE `playlist_party`
  ADD PRIMARY KEY (`id`);

--
-- indexes for the table `playlist_track`
--
ALTER TABLE `playlist_track`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_id` (`playlist_id`),
  ADD KEY `track_id` (`track_id`);

--
-- indexes for the table `remote`
--
ALTER TABLE `remote`
  ADD PRIMARY KEY (`id`);

--
-- indexes for the table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- indexes for the table `track`
--
ALTER TABLE `track`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `artist_id_2` (`artist_id`);

--
-- indexes for the table `download`
--
ALTER TABLE `download`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`);

--
-- AUTO_INCREMENT for exported tables
--

--
-- AUTO_INCREMENT for the table `album`
--
ALTER TABLE `album`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for the table `artist`
--
ALTER TABLE `artist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for the table `playlist`
--
ALTER TABLE `playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for the table `playlist_party`
--
ALTER TABLE `playlist_party`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for the table `playlist_track`
--
ALTER TABLE `playlist_track`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for the table `remote`
--
ALTER TABLE `remote`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for the table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for the table `track`
--
ALTER TABLE `track`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for the table `download`
--
ALTER TABLE `download`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `fk_album_to_artist` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `playlist_track`
--
ALTER TABLE `playlist_track`
  ADD CONSTRAINT `fk_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_playlist_track` FOREIGN KEY (`track_id`) REFERENCES `track` (`id`);

--
-- Constraints der Tabelle `track`
--
ALTER TABLE `track`
  ADD CONSTRAINT `fk_album` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_artist` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `download`
--
ALTER TABLE `download`
  ADD CONSTRAINT `fk_download_track` FOREIGN KEY (`track_id`) REFERENCES `track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


INSERT INTO `setting` (`id`, `identifier`, `value`) VALUES
(1, 'password_user', ''),
(2, 'password_remoteplayer', ''),
(3, 'password_voter', '');
