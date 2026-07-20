// cache data of the annotator
const annotatorCache = {
};

// look if the record is in the variable cache
function getCache(pageId, annotatorId, pointId) {
  return annotatorCache?.[pageId]?.[annotatorId]?.[pointId];
}

// put records in to the variable cache
function setCache(pageId, annotatorId, data) {
  if (!annotatorCache[pageId]) annotatorCache[pageId] = {};

  annotatorCache[pageId][annotatorId] = data;
}

//main functions of the block
async function annotatorBlock(annotator) {
  annotator._state = {
    canMove: false,
    isDragging: false,
    startX: 0,
    startY: 0
  };

  //id of the block + get type based on what is given
  let annotatorId = annotator.dataset.blockid;
  let annotatorIdType = "block";
  if(annotatorId == "" || annotatorId == null) {
    annotatorId = annotator.dataset.key;
    annotatorIdType = "field";
  }

  // get page id to get values later out of cache
  let pageId = annotator.dataset.pagekey;

  const annotationPoints = annotator.querySelectorAll('.annotator-pointer-selector');

  // move with slider arrows
  const slider = annotator.querySelector('.annotation-pagination');
  sliderFunction(slider, annotationPoints, annotator, annotatorId, annotatorIdType, pageId);

  // move trough the points with clicks
  annotationPoints.forEach(point => {
    point.addEventListener("click", () => {
      annotatorPoint(point, annotationPoints, annotator, annotatorId, annotatorIdType, pageId);
    });
  });

  moveInImage(annotator);

  const closeIcon = annotator.querySelector('.annotator-close-info');
  if(closeIcon) {
    closeInfo(closeIcon, annotator);
  }

  const openIcon = annotator.querySelector('.annotator-open-info');
  if(openIcon) {
    openInfo(annotator, openIcon);
  }

  //load all data
  await getNewData(0, annotatorId, annotatorIdType, pageId);
}

// if someone clicks on a point get the correct data and show
async function annotatorPoint(point, allPoints, annotator, annotatorId, annotatorIdType, pageId) {
  if(annotator._state.isDragging) return;
  const pointId = point.dataset.id;
  const data = await getNewData(pointId, annotatorId, annotatorIdType, pageId);

  const wrapperInfoPlacer = annotator.querySelector('.annotator-info-place');
  wrapperInfoPlacer.innerHTML = data;

  sliderVisualisation(annotator, pointId);

  zoom(point.dataset.x, point.dataset.y, point.dataset.zoom, annotator, allPoints);
  setActivePoint(pointId, allPoints);
}

// get the arrows and call the correct function after click
function sliderFunction(slider, allPoints, annotator, annotatorId, annotatorIdType, pageId) {
  if (!slider) return;

  const prevArrow = slider.querySelector(".prev-arrow");
  prevArrow.addEventListener("click", () => {
    prevItem(slider, allPoints, annotator, annotatorId, annotatorIdType, pageId)
  });

  const NextIcon = slider.querySelector(".next-arrow");
  NextIcon.addEventListener("click", () => {
    nextItem(slider, allPoints, annotator, annotatorId, annotatorIdType, pageId);
  });
}

// get the next item and show the correct data
async function nextItem(slider, allPoints, annotator, annotatorId, annotatorIdType, pageId) {
  if(annotator._state.isDragging) return;
  const page = slider.dataset.page;
  const maxPages = slider.dataset.pages;

  if(page != maxPages) {
    const number = parseInt(page) + 1;
    const data = await getNewData(number, annotatorId, annotatorIdType, pageId);

    const wrapperInfoPlacer = annotator.querySelector('.annotator-info-place');
    wrapperInfoPlacer.innerHTML = data;

    sliderVisualisation(annotator, number);

    const point = annotator.querySelector('[data-id="' + number + '"]');

    zoom(point.dataset.x, point.dataset.y, point.dataset.zoom, annotator, allPoints);
    setActivePoint(number, allPoints);
  }
}

// get the previous item and show the correct data
async function prevItem(slider, allPoints, annotator, annotatorId, annotatorIdType, pageId) {
  if(annotator._state.isDragging) return;
  const page = slider.dataset.page;

  if(page != 0) {
    const number = parseInt(page) - 1;
    const data = await getNewData(number, annotatorId, annotatorIdType, pageId);

    const wrapperInfoPlacer = annotator.querySelector('.annotator-info-place');
    wrapperInfoPlacer.innerHTML = data;

    sliderVisualisation(annotator, number);

    if(number != 0) {
      const point = annotator.querySelector('[data-id="' + number + '"]');
      zoom(point.dataset.x, point.dataset.y, point.dataset.zoom, annotator, allPoints);
    }
    else {
      zoom(0, 0, 0, annotator, allPoints);
    }

    setActivePoint(number, allPoints);
  }
}

// design the arrows in the slider based on the active item
function sliderVisualisation(annotator, pointId) {
  const slider = annotator.querySelector('.annotation-pagination');
  slider.dataset.page = pointId;

  if(pointId == slider.dataset.pages) {
    slider.classList.add('last');
  }
  else {
    slider.classList.remove('last');
  }
}

// make a point visualy active
function setActivePoint(pointId, annotationPoints) {
  annotationPoints.forEach(point => {
    if(point.dataset.id == pointId) {
      point.classList.add('active');
    }
    else {
      point.classList.remove('active');
    }
  })
}

//get data of a point
async function getNewData(pointId, annotatorId, annotatorIdType, pageId) {
  // look if the data is in the cache if so use this one
  const cached = getCache(pageId, annotatorId, pointId);
  if (cached) return cached;

  // if not in the cache get the data from the backend and put it in the cache
  const language = document.documentElement.lang;
  const url = `/content/annotator/data/${annotatorId}/${annotatorIdType}/${language}`;
  let returnData = "";

  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Accept': 'application/json' },
    });

    if (!response.ok) {
      throw new Error("Failed loading data of point");
    }

    const data = await response.json();
    setCache(pageId, annotatorId, data.data);
    return getCache(pageId, annotatorId, pointId);
  }
  catch (error) {
    return null;
    console.log("Failed loading data of point");
  }
}

function zoom(xValue, yValue, zoomFactor, annotator, points) {
  annotator.dataset.zoomFactor = zoomFactor;

  // get canvas and viewport
  const canvas = annotator.querySelector('.annotator__canvas');
  const viewport = annotator.querySelector('.annotator__viewport');

  //put it first back to the normal size
  canvas.style.transform = "translate(0px, 0px) scale(1)";

  // get data like width and height of canvas and viewport and store them
  const width = canvas.offsetWidth;
  const height = canvas.offsetHeight;

  const { width: viewportwidth, height: viewportheight } = viewport.getBoundingClientRect();

  // calculate zoom factor (no zoom is total of 1)
  let scale = 1 + (zoomFactor * 0.5);

  // calculate the coordinates of the points in px
  let x = width * (xValue / 100);
  let y = height * (yValue / 100);

  // get the transform of the image to get that point in the center of the viewport (scale calculated)
  let translateX = (viewportwidth / 2) - (x * scale);
  let translateY = (viewportheight / 2) - (y * scale);

  // get boundaries of the image based on the viewport
  const minX = viewportwidth - (width * scale);
  const minY = viewportheight - (height * scale);

  //let the image not go out my viewport
  translateX = Math.min(0, Math.max(minX, translateX));
  translateY = Math.min(0, Math.max(minY, translateY));

  canvas.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;  
  annotator.dataset.translateX = translateX;
  annotator.dataset.translateY = translateY;
  annotator.dataset.scale = scale;

  resetZoomPoints(points, scale);
  moveInImage(annotator);
}

//set all the points back to the original size after zooming the view
function resetZoomPoints(points, scale) {
  points.forEach(element => {
    const pointScale = 1 / scale;
    element.style.transform = `translate(-50%, -50%) scale(${pointScale})`;
  });
}

function moveInImage(annotator) {
  const imageViewport = annotator.querySelector('.annotator__viewport');

  if(parseFloat(annotator.dataset.zoomFactor) > 0.0) {
    annotator._state.canMove = true;
    imageViewport.style.cursor = "grab";

    imageViewport.onmousedown = (e) => startDrag(e, imageViewport, annotator);
  }
  else {
    annotator._state.canMove = false;
    imageViewport.style.cursor = "auto";
  }
}

function startDrag(e, viewport, annotator) {
  annotator._state.isDragging = true;

  annotator._state.startX = e.clientX;
  annotator._state.startY = e.clientY;
  viewport.style.cursor = "grabbing";

  const moveHandler = (e) => dragging(e, annotator, viewport);
  const upHandler = (e) => endDrag(e, annotator, viewport, moveHandler, upHandler);

  viewport.addEventListener('mousemove', moveHandler);
  viewport.addEventListener('mouseup', upHandler);

}

function dragging(e, annotator, viewport) {
  if (!annotator._state.isDragging) return;
  const canvas = annotator.querySelector('.annotator__canvas');
  let currentX = parseFloat(annotator.dataset.translateX);
  let currentY = parseFloat(annotator.dataset.translateY);
  let scale = parseFloat(annotator.dataset.scale);

  const draggingX = e.clientX - annotator._state.startX;
  const draggingY = e.clientY - annotator._state.startY;

  let newImageXvalue = currentX + draggingX;
  let newImageYvalue = currentY + draggingY;

  const { width: viewportwidth, height: viewportheight } = viewport.getBoundingClientRect();
  const width = canvas.offsetWidth;
  const height = canvas.offsetHeight;

  const minX = viewportwidth - (width * scale);
  const minY = viewportheight - (height * scale);

  newImageXvalue = Math.min(0, Math.max(minX, newImageXvalue));
  newImageYvalue = Math.min(0, Math.max(minY, newImageYvalue));

  canvas.style.transform = `translate(${newImageXvalue}px, ${newImageYvalue}px) scale(${scale})`;  
}

function endDrag(e, annotator, viewport, moveHandler, upHandler) {
  annotator._state.isDragging = false;
  viewport.style.cursor = "grab";

  const canvas = annotator.querySelector('.annotator__canvas');

  const transform = canvas.style.transform;
  const match = transform.match(/translate\(([-0-9.]+)px,\s*([-0-9.]+)px\)/);

  if (match) {
    annotator.dataset.translateX = match[1];
    annotator.dataset.translateY = match[2];
  }

  viewport.removeEventListener('mousemove', moveHandler);
  viewport.removeEventListener('mouseup', upHandler);
}

function closeInfo(closeIcon, annotator) {
  const infoSection = annotator.querySelector('.annotator-info-section');
  const openIcon = annotator.querySelector('.annotator-open-info');

  closeIcon.addEventListener("click", () => {
    openIcon.classList.remove('hidden');
    infoSection.classList.add('hidden');
  });
}

function openInfo(annotator, openIcon) {
  const infoSection = annotator.querySelector('.annotator-info-section');
  const closeIcon = annotator.querySelector('.annotator-close-info');

  openIcon.addEventListener("click", () => {
    openIcon.classList.add('hidden');
    infoSection.classList.remove('hidden');
  });
}

export { annotatorBlock };